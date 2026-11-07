<?php

namespace Tests\Feature\Ruby;

use Modules\Common\App\Models\Contact;
use Modules\Sapphire\App\Models\User;

class ContactUpdateTest extends StaffTestCase
{
    private Contact $member;

    private User $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postAjax('r/contacts/create', $this->memberPayload());
        $this->member = Contact::firstOrFail();
        $this->account = User::whereUsername('janedoe')->firstOrFail();
    }

    private function updatePayload(array $overrides = []): array
    {
        return $overrides + [
            'id' => $this->member->id,
            'name' => 'Jane Doe',
            'job' => 'Senior Accountant',
            'emails' => [
                ['id' => $this->member->emails[0]->id, 'address' => 'jane@example.com', 'default' => '1'],
            ],
            'phones' => [
                ['id' => $this->member->phones[0]->id, 'number' => '0123456789', 'default' => '1'],
            ],
            'roles' => [$this->hrRole()->id],
            'applicant_mode' => 'existing',
            'applicant_id' => $this->member->id,
            'username' => 'janedoe',
        ];
    }

    public function test_edit_payload_is_prefillable(): void
    {
        $response = $this->get("r/contacts/{$this->member->id}");

        $response->assertOk();
        $this->assertSame('janedoe', $response->json('username'));
        $this->assertSame([$this->hrRole()->id], $response->json('roles_ids'));
        $this->assertIsArray($response->json('departments_member'));
        $this->assertIsArray($response->json('departments_managed'));
        $this->assertSame(2, count($response->json('emails')));
        $this->assertSame(2, count($response->json('phones')));
        $this->assertNotNull($response->json('applicant'));
    }

    public function test_update_syncs_collections_atomically(): void
    {
        $response = $this->postAjax('r/contacts/update', $this->updatePayload([
            'job' => 'Chief Accountant',
            'emails' => [
                ['address' => 'new@example.com', 'default' => '1'],
            ],
        ]));

        $response->assertOk();
        $member = $this->member->fresh();
        $this->assertSame('Chief Accountant', $member->job);
        $this->assertSame(1, $member->emails()->count());
        $this->assertSame('new@example.com', $member->emails->first()->address);
        $this->assertSame(1, $member->phones()->count(), 'untouched collections survive');
    }

    public function test_update_keeps_password_when_omitted(): void
    {
        $hashBefore = $this->account->password;

        $this->postAjax('r/contacts/update', $this->updatePayload())->assertOk();

        $this->assertSame($hashBefore, $this->account->fresh()->password);
    }

    public function test_update_keeping_roles_unchanged_retains_them(): void
    {
        // The edit form prefills the roles select with the member's current
        // roles; resubmitting them unchanged must keep contact and account
        // in sync without demoting the member.
        $this->postAjax('r/contacts/update', $this->updatePayload())->assertOk();

        $member = $this->member->fresh();
        $this->assertTrue($member->hasRole('hr-manager'), 'contact keeps its role');
        $this->assertTrue($member->user->hasRole('hr-manager'), 'account keeps its role');
    }

    public function test_update_without_roles_is_rejected(): void
    {
        $response = $this->postAjax('r/contacts/update', $this->updatePayload(['roles' => null]));

        $response->assertStatus(422)->assertJsonValidationErrors(['roles']);
        $this->assertTrue($this->member->fresh()->hasRole('hr-manager'), 'roles untouched on rejection');
    }

    public function test_update_changes_password_when_provided(): void
    {
        $this->postAjax('r/contacts/update', $this->updatePayload([
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]))->assertOk();

        $this->assertNotSame($this->account->password, $this->account->fresh()->password);
    }

    public function test_update_rejects_taken_username(): void
    {
        User::create(['username' => 'taken-name', 'password' => 'other-password', 'active' => true]);

        $response = $this->postAjax('r/contacts/update', $this->updatePayload(['username' => 'taken-name']));

        $response->assertStatus(422)->assertJsonValidationErrors(['username']);
        $this->assertSame('janedoe', $this->member->fresh()->user->username, 'nothing changed');
    }

    public function test_document_title_can_be_renamed(): void
    {
        $document = $this->sampleDocument();
        $this->postAjax('r/contacts/update', $this->updatePayload(['documents' => [$document]]))->assertOk();
        $stored = $this->member->applicant->documents->first();

        $this->postAjax('r/contacts/update', $this->updatePayload([
            'document_titles' => [$stored->id => 'Renamed Credential'],
        ]))->assertOk();

        $this->assertSame('Renamed Credential', $stored->fresh()->title);
    }
}
