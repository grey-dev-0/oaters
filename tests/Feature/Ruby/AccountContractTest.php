<?php

namespace Tests\Feature\Ruby;

use Modules\Common\App\Models\Contact;
use Modules\Sapphire\App\Models\User;
use Modules\Sapphire\App\Services\AccountException;
use Modules\Sapphire\App\Services\AccountManager;

class AccountContractTest extends StaffTestCase
{
    public function test_provisioned_member_can_sign_in_to_ruby(): void
    {
        $this->postAjax('r/contacts/create', $this->memberPayload(['username' => 'signable', 'password' => 'secret-password', 'password_confirmation' => 'secret-password']))->assertOk();

        auth()->logout();
        $response = $this->post('r/login', ['username' => 'signable', 'password' => 'secret-password']);

        $response->assertRedirect('r');
        $this->assertAuthenticatedAs(User::whereUsername('signable')->first());
    }

    public function test_suspended_member_cannot_sign_in(): void
    {
        $this->postAjax('r/contacts/create', $this->memberPayload(['username' => 'suspended', 'password' => 'secret-password', 'password_confirmation' => 'secret-password']))->assertOk();
        $contact = Contact::first();
        $contact->punches()->create(['type' => 'in']);
        $this->postAjax('r/contacts/delete', ['id' => $contact->id]);
        $this->assertTrue($contact->punches()->exists(), 'deactivated via history');

        auth()->logout();
        $response = $this->post('r/login', ['username' => 'suspended', 'password' => 'secret-password']);

        $this->assertGuest();
        $response->assertOk();
    }

    public function test_provision_rejects_duplicate_username(): void
    {
        $this->expectException(AccountException::class);
        AccountManager::provision(999, 'tester', 'irrelevant');
    }

    public function test_suspend_and_restore_toggle_account_access(): void
    {
        $this->postAjax('r/contacts/create', $this->memberPayload())->assertOk();
        $contact = Contact::first();

        AccountManager::suspend($contact->id);
        $this->assertFalse($contact->user->fresh()->active);

        AccountManager::restore($contact->id);
        $this->assertTrue($contact->user->fresh()->active);
    }
}
