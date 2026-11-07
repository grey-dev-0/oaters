<?php

namespace Modules\Ruby\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Common\App\Models\City;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\CountryLocale;
use Modules\Ruby\App\Http\Requests\ContactRequest;
use Modules\Ruby\App\Models\DegreeLocale;
use Modules\Ruby\App\Models\Notice;
use Modules\Ruby\App\Models\PayrollPayment;
use Modules\Ruby\App\Models\Salary;
use Modules\Ruby\App\Models\Subordinate;
use Modules\Sapphire\App\Models\RoleLocale;
use Modules\Sapphire\App\Services\AccountManager;

class ContactController extends Controller
{
    public function postSearch()
    {
        $rolesGroup = ['general-manager', 'financial-manager', 'hr-manager', 'accountant', 'hr-assistant', 'employee',
            'director', 'team-lead'];
        $suggestions = Contact::where(fn ($q) => $q->where('name', 'like', '%'.($query = request('query')).'%')->orWhereHas('emails', fn ($emails) => $emails->where('address', 'like', "%$query%")))->whereHas('roles', fn ($roles) => $roles->whereIn('name', $rolesGroup))->with(['emails' => fn ($emails) => $emails->whereDefault(true)])->limit(request('limit'))->get(['id', 'name'])->map(fn ($contact) => $contact->only('id') + ['name' => "{$contact->name} - {$contact->emails->first()?->address}"])->pluck('name', 'id');

        return response()->json(compact('suggestions'));
    }

    public function getIndex()
    {
        $locale = app()->getLocale();
        $roles = RoleLocale::whereLocale($locale)->whereHas('role', fn ($role) => $role->whereIn('name', ['general-manager', 'financial-manager', 'hr-manager', 'director', 'team-lead', 'accountant', 'hr-assistant', 'employee']))->pluck('title', 'role_id');
        $departments = \Modules\Ruby\App\Models\DepartmentLocale::whereLocale($locale)->pluck('name', 'department_id');
        $degrees = DegreeLocale::whereLocale($locale)->pluck('name', 'degree_id');
        $countries = CountryLocale::whereLocale($locale)->pluck('name', 'country_id');

        // Cities are intentionally NOT listed here: the catalog is huge and
        // is searched remotely through postCities() from the staff form.
        return view('ruby::contacts', compact('roles', 'departments', 'degrees', 'countries'));
    }

    public function postIndex()
    {
        $contacts = Contact::activeRecruit()->withRoles()->withDefaultInfo()->withDepartments()->with('applicant:id,recruited_at')->select(['lc_contacts.id', 'name', 'job', 'deleted_at']);
        if (request('status') == 'inactive') {
            $contacts->onlyTrashed();
        }

        return tap(\DataTables::of($contacts), fn ($dataTable) => \DataTablesHelper::formatTimestampColumns($dataTable, ['applicant.recruited_at']))->filter(function ($query) {
            \DataTablesHelper::filterByDate($query, ['applicant.recruited_at']);
            $columns = \DataTablesHelper::getColumns();
            if (! empty($departments = isset($columns['departments']) ? request("columns.{$columns['departments']}.search.value") : [request('department')])) {
                $query->where(fn ($q) => $q->whereHas('departments', fn ($depts) => $depts->whereIn('r_departments.id', $departments))
                    ->orWhereHas('managed_departments', fn ($depts) => $depts->whereIn('r_departments.id', $departments)));
            }
            if (! empty($roles = request("columns.{$columns['roles']}.search.value"))) {
                $query->whereHas('roles', fn ($r) => $r->whereIn('id', $roles));
            }
        })->addColumn('actions', fn () => view('ruby::actions.contacts'))->orderColumn('roles', function ($query, $direction) {
            $query->leftJoin('s_model_has_roles AS mr', fn ($join) => $join->on('lc_contacts.id', 'mr.model_id')
                ->where('model_type', Contact::class))->leftJoin('s_role_locales AS rl', 'mr.role_id', 'rl.role_id')->orderBy('rl.title', $direction);
        })->rawColumns(['actions'])->make();
    }

    public function postCreateOrUpdate(ContactRequest $request)
    {
        return $request->handle();
    }

    public function getContact($contact)
    {
        $contact = Contact::withTrashed()->withRoles()->withAllInfo()->withDepartments()->withRecruitmentDetails()
            ->with(['leaves' => fn ($leaves) => $leaves->select(['contact_id', 'type', 'starts_at', 'ends_at'])
                ->whereStatus(1)->take(5)->orderBy('id', 'desc')])->find($contact);
        $contact->applicant?->setAttribute('recruited_at_formatted',
            $contact->applicant->recruited_at->format('d/m/Y h:i:s A'));
        $contact->applicant?->documents->each(fn ($document) => $document->setAppends(['download_url']));

        return response()->json($contact->setAttribute('roles_ids', $contact->roles->pluck('id'))
            ->setAttribute('departments_member', $contact->memberships->pluck('department_id')->unique()->values())
            ->setAttribute('departments_managed', Subordinate::whereManagerId($contact->id)->whereNull('contact_id')->pluck('department_id'))
            ->setAttribute('username', AccountManager::username($contact->id))
            ->setAttribute('image_url', $contact->image_url));
    }

    public function getContactImage($filename)
    {
        return response()->file(Storage::disk('local')->path("c_images/$filename"));
    }

    /**
     * Searches applicant records that are not recruited yet, for the staff
     * form's applicant picker (FR-004 - already-linked applicants excluded).
     */
    public function postApplicants()
    {
        $suggestions = Contact::whereHas('applicant', fn ($applicant) => $applicant->whereNull('recruited_at'))
            ->where('name', 'like', '%'.request('query').'%')->orderBy('name')
            ->limit(request('limit', 10))->get(['id', 'name'])->pluck('name', 'id');

        return response()->json(compact('suggestions'));
    }

    /**
     * Searches localized city names, for the staff form's address rows.
     */
    public function postCities()
    {
        $locale = app()->getLocale();
        $suggestions = City::whereHas('translations', fn ($translations) => $translations->where('locale', $locale)
            ->where('name', 'like', '%'.request('query').'%'))->limit(request('limit', 10))->get()
            ->mapWithKeys(fn ($city) => [$city->id => $city->{"name:$locale"}])->all();

        return response()->json(compact('suggestions'));
    }

    /**
     * Removes a staff member: deactivated (retained) when any historical
     * record exists, permanently removed otherwise (FR-008). Departments
     * the member managed always survive, managerless.
     */
    public function postDelete(Request $request)
    {
        $contact = Contact::findOrFail($request->input('id'));
        $deactivated = DB::transaction(function () use ($contact) {
            $history = $this->hasHistory($contact);
            $contact->memberships()->delete();
            Subordinate::whereManagerId($contact->id)->delete();
            $contact->shifts()->detach();

            if ($history) {
                $contact->delete();
                AccountManager::suspend($contact->id);
            } else {
                AccountManager::delete($contact->id);
                $contact->applicant?->documents->each(fn ($document) => Storage::disk('local')->delete("r_documents/$document->filename"));
                $contact->forceDelete();
            }

            return $history;
        });

        return response()->json(['success' => true, 'message' => trans('ruby::contacts.'.($deactivated ? 'deactivated' : 'deleted'))]);
    }

    /**
     * Reactivates a deactivated staff member and restores their account
     * access. Department memberships are intentionally not restored.
     */
    public function postRestore(Request $request)
    {
        $contact = Contact::onlyTrashed()->findOrFail($request->input('id'));
        DB::transaction(function () use ($contact) {
            $contact->restore();
            AccountManager::restore($contact->id);
        });

        return response()->json(['success' => true, 'message' => trans('ruby::contacts.reactivated')]);
    }

    /**
     * Renders the organizational structure chart. Every row of
     * r_subordinates is a manager-member edge grouped by department;
     * manager-only rows (NULL contact_id) and edges referencing
     * deactivated (soft-deleted) members carry no node and are skipped.
     */
    public function getStructure()
    {
        $locale = app()->getLocale();
        $subordinates = Subordinate::with(['manager', 'contact',
            'department.translations' => fn ($locales) => $locales->whereLocale($locale)])
            ->get()
            ->filter(fn ($subordinate) => $subordinate->manager && $subordinate->contact)
            ->map(fn ($subordinate) => [
                'manager' => $subordinate->manager->only(['id', 'name']),
                'member' => $subordinate->contact->only(['id', 'name']),
                'department' => [
                    'id' => $subordinate->department_id,
                    'name' => $subordinate->department?->{"name:$locale"},
                ],
            ])->values();

        return view('ruby::structure', compact('subordinates'));
    }

    /**
     * Determines whether the member has any historical record that must be
     * retained (data-model.md): attendance, leaves, documents, notices or
     * payroll references.
     */
    private function hasHistory(Contact $contact): bool
    {
        return $contact->punches()->exists() || $contact->leaves()->exists() || $contact->applicant?->documents()->exists()
            || Notice::where(fn ($query) => $query->where('author_id', $contact->id)->orWhere('contact_id', $contact->id))->exists()
            || Salary::whereKey($contact->id)->exists() || PayrollPayment::where('salary_id', $contact->id)->exists();
    }
}
