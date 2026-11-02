<?php

namespace Tests\Feature\Ruby;

use Illuminate\Support\Facades\Storage;
use Modules\Common\App\Models\Contact;
use Modules\Sapphire\App\Models\User;

class ContactCreateTest extends StaffTestCase
{
    public function test_create_persists_member_collections_and_account(): void
    {
        $document = $this->sampleDocument();

        $response = $this->postAjax('r/contacts/create', $this->memberPayload(['documents' => [$document]]));

        $response->assertOk()->assertJsonPath('success', true);
        $contact = Contact::firstOrFail();
        $this->assertSame('Jane Doe', $contact->name);

        $this->assertSame(2, $contact->emails()->count());
        $this->assertSame(1, $contact->emails()->whereDefault(true)->count());
        $this->assertSame('jane@example.com', $contact->emails()->whereDefault(true)->first()->address);
        $this->assertSame(1, $contact->phones()->whereDefault(true)->count());
        $this->assertSame(1, $contact->addresses()->count());

        $this->assertTrue($contact->applicant->recruited_at->isSameDay('2026-01-05'));
        $this->assertSame(2015, (int) $contact->applicant->degree_date);
        $this->assertSame(1, $contact->applicant->documents()->count());
        Storage::disk('local')->assertExists('r_documents/'.$contact->applicant->documents->first()->filename);

        $user = User::whereUsername('janedoe')->first();
        $this->assertNotNull($user);
        $this->assertSame($user->id, $contact->user_id);
        $this->assertTrue($user->hasRole('hr-manager'), 'account carries the selected roles');
        $this->assertTrue($contact->hasRole('hr-manager'), 'contact carries the position role');
    }

    public function test_create_rejects_missing_required_fields(): void
    {
        $response = $this->postAjax('r/contacts/create', $this->memberPayload(['name' => '']));

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
        $this->assertSame(0, Contact::count(), 'nothing persisted on rejection');
        $this->assertSame(1, User::count(), 'only the acting user exists');
    }

    public function test_duplicate_username_aborts_everything(): void
    {
        User::create(['username' => 'janedoe', 'password' => 'other-password', 'active' => true]);

        $response = $this->postAjax('r/contacts/create', $this->memberPayload());

        $response->assertStatus(422)->assertJsonValidationErrors(['username']);
        $this->assertSame(0, Contact::count(), 'all-or-nothing: no staff record survives the account failure');
    }

    public function test_multiple_defaults_are_normalized_to_one(): void
    {
        $payload = $this->memberPayload();
        $payload['emails'] = [
            ['address' => 'a@example.com', 'default' => '1'],
            ['address' => 'b@example.com', 'default' => '1'],
        ];

        $this->postAjax('r/contacts/create', $payload)->assertOk();

        $contact = Contact::firstOrFail();
        $this->assertSame(1, $contact->emails()->whereDefault(true)->count());
        $this->assertSame('a@example.com', $contact->emails()->whereDefault(true)->first()->address);
    }

    public function test_existing_applicant_link_marks_them_recruited(): void
    {
        $applicant = Contact::create(['name' => 'Prior Applicant', 'job' => 'Recruit']);
        $applicant->applicant()->create([
            'country_id' => $this->makeCountry()->id,
            'degree_id' => $this->makeDegree()->id,
            'degree_date' => 2010,
            'recruited_at' => null,
        ]);

        $this->postAjax('r/contacts/create', $this->memberPayload([
            'applicant_mode' => 'existing',
            'applicant_id' => $applicant->id,
        ]))->assertOk();

        $this->assertNotNull($applicant->applicant->fresh()->recruited_at, 'linked applicant became a recruited member');
        $this->assertSame(1, Contact::count(), 'no duplicate contact is created when linking');
    }
}
