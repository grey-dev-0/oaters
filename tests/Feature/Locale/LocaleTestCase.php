<?php

namespace Tests\Feature\Locale;

use Modules\Common\App\Models\Contact;
use Modules\Sapphire\App\Models\User;
use Tests\Feature\Ruby\StaffTestCase;

class LocaleTestCase extends StaffTestCase
{
    /**
     * Signs in a user whose platform account is linked to a contact, so the
     * account-stored preference is resolvable through the account boundary.
     */
    protected function actingAsContactUser(string $locale = 'en', string $username = 'locale-user'): User
    {
        $user = User::create([
            'username' => $username,
            'password' => \Illuminate\Support\Facades\Hash::make('secret-password'),
            'active' => true,
            'locale' => $locale,
        ]);
        $user->assignRole($this->hrRole());

        $contact = Contact::create(['name' => 'Locale User', 'job' => 'Tester']);
        Contact::whereKey($contact->id)->update(['user_id' => $user->id]);

        $this->actingAs($user);

        return $user;
    }
}
