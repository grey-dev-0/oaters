<?php

namespace Modules\Ruby\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Applicant;
use Modules\Ruby\App\Models\Document;
use Modules\Sapphire\App\Services\AccountException;
use Modules\Sapphire\App\Services\AccountManager;

class ContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'job' => ['required', 'string', 'min:2', 'max:255'],
            'image' => ['nullable', 'image'],

            'emails' => ['required', 'array', 'min:1'],
            'emails.*.id' => ['nullable', 'integer', 'exists:lc_emails,id'],
            'emails.*.address' => ['required', 'email', 'max:255', 'distinct'],
            'emails.*.default' => ['nullable', 'boolean'],

            'phones' => ['required', 'array', 'min:1'],
            'phones.*.id' => ['nullable', 'integer', 'exists:lc_phones,id'],
            'phones.*.number' => ['required', 'string', 'max:16', 'distinct'],
            'phones.*.default' => ['nullable', 'boolean'],

            'addresses' => ['nullable', 'array'],
            'addresses.*.id' => ['nullable', 'integer', 'exists:lc_addresses,id'],
            'addresses.*.city_id' => ['required', 'integer', 'exists:lc_cities,id'],
            'addresses.*.line_1' => ['nullable', 'string', 'max:255'],
            'addresses.*.line_2' => ['nullable', 'string', 'max:255'],

            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:'.\Modules\Sapphire\App\Models\Role::class.',id'],

            'member_departments' => ['nullable', 'array'],
            'member_departments.*' => ['integer', 'exists:r_departments,id'],
            'managed_departments' => ['nullable', 'array'],
            'managed_departments.*' => ['integer', 'exists:r_departments,id'],

            'applicant_mode' => ['required', 'in:existing,new'],
            'applicant_id' => [
                'required_if:applicant_mode,existing', 'nullable',
                $this->route()->getName() == 'ruby::contacts.update'
                    // The member's own applicant record - already recruited.
                    ? Rule::exists('r_applicants', 'id')->where('id', $this->id)
                    // A not-yet-recruited applicant becomes the new staff member.
                    : Rule::exists('r_applicants', 'id')->whereNull('recruited_at'),
            ],
            'nationality' => ['required_if:applicant_mode,new', 'nullable', 'integer', 'exists:lc_countries,id'],
            'degree' => ['required_if:applicant_mode,new', 'nullable', 'integer', 'exists:r_degrees,id'],
            'degree_date' => ['nullable', 'integer', 'digits:4'],
            'recruited_at' => ['nullable', 'date'],

            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file'],
            'document_new_titles' => ['nullable', 'array'],
            'document_new_titles.*' => ['nullable', 'string', 'max:255'],
            'document_titles' => ['nullable', 'array'],
            'document_titles.*' => ['nullable', 'string', 'max:255'],
            'documents_removed' => ['nullable', 'array'],
            'documents_removed.*' => ['integer', 'exists:r_documents,id'],

            'username' => ['required', 'string', 'min:3', 'max:255'],
            'password' => [$this->route()->getName() == 'ruby::contacts.update' ? 'nullable' : 'required', 'nullable', 'confirmed', 'min:6'],
        ];
        if ($this->route()->getName() == 'ruby::contacts.update') {
            $rules += ['id' => ['required', 'integer', 'exists:lc_contacts,id']];
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handles staff member create / update requests. The whole operation is
     * transactional: an account provisioning failure (or any other exception)
     * aborts everything and nothing is persisted (all-or-nothing per FR-010).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle()
    {
        try {
            DB::transaction(function () {
                if ($this->id) {
                    $contact = tap(Contact::findOrFail($this->id), fn ($contact) => $contact->fill($this->only('name', 'job'))->save());
                } elseif ($this->applicant_mode == 'existing') {
                    // The picked applicant's contact IS the staff member being created.
                    $contact = tap(Contact::findOrFail($this->applicant_id), fn ($contact) => $contact->fill($this->only('name', 'job'))->save());
                } else {
                    $contact = Contact::create($this->only('name', 'job'));
                }

                $this->handleImage($contact);
                $this->syncEmails($contact);
                $this->syncPhones($contact);
                $this->syncAddresses($contact);
                $contact->syncRoles($this->roles);
                $this->syncDepartments($contact);
                $applicant = $this->handleApplicant($contact);
                $this->handleDocuments($applicant);

                if ($this->id) {
                    AccountManager::update($contact->id, $this->username, $this->password, $this->roles);
                } else {
                    AccountManager::provision($contact->id, $this->username, $this->password, $this->roles);
                }
            });
        } catch (AccountException $exception) {
            if ($exception->getMessage() == 'username-taken') {
                throw \Illuminate\Validation\ValidationException::withMessages(['username' => trans('ruby::contacts.username_taken')]);
            }
            throw $exception;
        }

        return response()->json(['success' => true, 'message' => trans('ruby::contacts.'.($this->id ? 'updated' : 'created'))]);
    }

    /**
     * Stores the uploaded profile image, when present, on the staff record.
     *
     * @return void
     */
    private function handleImage(Contact $contact)
    {
        if ($this->hasFile('image')) {
            if (! empty($contact->image)) {
                Storage::disk('local')->delete("c_images/$contact->image");
            }
            $contact->fill(['image' => $this->file('image')->hashName()])->save();
            $this->file('image')->store('c_images', 'local');
        }
    }

    /**
     * Syncs the submitted emails, normalizing exactly one default (FR-002).
     *
     * @return void
     */
    private function syncEmails(Contact $contact)
    {
        $defaultIndex = $this->defaultIndex(collect($this->emails ?? []));
        $kept = [];
        foreach ($this->emails ?? [] as $index => $email) {
            $attributes = ['address' => $email['address'], 'default' => $index === $defaultIndex];
            if (! empty($email['id'])) {
                $contact->emails()->whereKey($email['id'])->update($attributes);
                $kept[] = $email['id'];
            } else {
                $kept[] = $contact->emails()->create($attributes)->id;
            }
        }
        $contact->emails()->whereNotIn('id', $kept)->delete();
    }

    /**
     * Syncs the submitted phones, normalizing exactly one default (FR-002).
     *
     * @return void
     */
    private function syncPhones(Contact $contact)
    {
        $defaultIndex = $this->defaultIndex(collect($this->phones ?? []));
        $kept = [];
        foreach ($this->phones ?? [] as $index => $phone) {
            $attributes = ['number' => $phone['number'], 'default' => $index === $defaultIndex];
            if (! empty($phone['id'])) {
                $contact->phones()->whereKey($phone['id'])->update($attributes);
                $kept[] = $phone['id'];
            } else {
                $kept[] = $contact->phones()->create($attributes)->id;
            }
        }
        $contact->phones()->whereNotIn('id', $kept)->delete();
    }

    /**
     * Syncs the submitted addresses.
     *
     * @return void
     */
    private function syncAddresses(Contact $contact)
    {
        $kept = [];
        foreach ($this->addresses ?? [] as $address) {
            $attributes = ['city_id' => $address['city_id'], 'line_1' => $address['line_1'] ?? null, 'line_2' => $address['line_2'] ?? null];
            if (! empty($address['id'])) {
                $contact->addresses()->whereKey($address['id'])->update($attributes);
                $kept[] = $address['id'];
            } else {
                $kept[] = $contact->addresses()->create($attributes)->id;
            }
        }
        $contact->addresses()->whereNotIn('id', $kept)->delete();
    }

    /**
     * Syncs department memberships (rows carrying the member) and pure
     * management rows (rows carrying the manager only) per r_subordinates.
     *
     * @return void
     */
    private function syncDepartments(Contact $contact)
    {
        $memberships = $this->member_departments ?: [];
        $contact->memberships()->whereNotIn('department_id', $memberships)->delete();
        foreach ($memberships as $departmentId) {
            $contact->memberships()->firstOrCreate(['department_id' => $departmentId, 'manager_id' => null]);
        }

        $managed = $this->managed_departments ?: [];
        \Modules\Ruby\App\Models\Subordinate::whereManagerId($contact->id)->whereNull('contact_id')
            ->whereNotIn('department_id', $managed)->delete();
        foreach ($managed as $departmentId) {
            \Modules\Ruby\App\Models\Subordinate::firstOrCreate(['department_id' => $departmentId, 'manager_id' => $contact->id, 'contact_id' => null]);
        }
    }

    /**
     * Links an existing applicant record (marking them recruited) or creates
     * the minimal inline applicant record (FR-004).
     */
    private function handleApplicant(Contact $contact): Applicant
    {
        $attributes = array_filter([
            'country_id' => $this->nationality,
            'degree_id' => $this->degree,
            'degree_date' => $this->degree_date,
            'recruited_at' => $this->recruited_at ?: now(),
        ], fn ($value) => ! empty($value));

        if ($this->applicant_mode == 'existing') {
            $applicant = Applicant::findOrFail($this->applicant_id);
        } else {
            $applicant = new Applicant();
            $applicant->id = $contact->id;
        }

        return tap($applicant, fn (Applicant $applicant) => $applicant->fill($attributes)->save());
    }

    /**
     * Uploads new employment documents, renames kept ones and removes
     * the ones dropped from the form.
     *
     * @return void
     */
    private function handleDocuments(Applicant $applicant)
    {
        Document::whereKey($this->documents_removed ?: [])->get()->each(fn (Document $document) => tap($document, fn ($document) => Storage::disk('local')->delete("r_documents/$document->filename"))->delete());

        foreach ($this->document_titles ?? [] as $id => $title) {
            if (! empty($title) && ! in_array($id, $this->documents_removed ?: [])) {
                $applicant->documents()->whereKey($id)->update(['title' => $title]);
            }
        }

        foreach ($this->file('documents', []) ?: [] as $index => $file) {
            $applicant->documents()->create([
                'title' => $this->input("document_new_titles.$index") ?: $file->getClientOriginalName(),
                'filename' => $file->hashName(),
            ]);
            $file->store('r_documents', 'local');
        }
    }

    /**
     * Gets the index of the row flagged default - falling back to the first row.
     *
     * @param  \Illuminate\Support\Collection  $rows
     * @return int|string|null
     */
    private function defaultIndex($rows)
    {
        return $rows->search(fn ($row) => ! empty($row['default'])) ?: 0;
    }
}
