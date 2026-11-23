<?php

namespace Tests\Feature\Locale;

use Modules\Sapphire\App\Models\User;

class LocaleSwitchTest extends LocaleTestCase
{
    public function test_switching_requires_authentication(): void
    {
        auth()->logout();

        $this->post('locale', ['locale' => 'ar'])->assertRedirect();

        $this->assertGuest();
    }

    public function test_unsupported_locales_are_rejected(): void
    {
        $this->postAjax('locale', ['locale' => 'fr'])->assertStatus(422);
    }

    public function test_switching_updates_the_session_and_the_account_together(): void
    {
        $user = $this->actingAsContactUser('en');

        $this->postAjax('locale', ['locale' => 'ar'])->assertRedirect();

        $this->assertSame('ar', session('locale'));
        $this->assertSame('ar', User::whereKey($user->id)->value('locale'));
    }

    public function test_switching_back_to_english_updates_both_stores(): void
    {
        $user = $this->actingAsContactUser('ar');

        $this->postAjax('locale', ['locale' => 'en'])->assertRedirect();

        $this->assertSame('en', session('locale'));
        $this->assertSame('en', User::whereKey($user->id)->value('locale'));
    }

    public function test_the_switch_survives_in_module_navigation(): void
    {
        $this->actingAsContactUser('en');
        $this->postAjax('locale', ['locale' => 'ar'])->assertRedirect();

        $this->get('r/attendance')->assertOk()->assertSee('lang="ar"', false);
        $this->get('r/departments')->assertOk()->assertSee('lang="ar"', false);
    }

    public function test_switching_does_not_disturb_other_live_sessions(): void
    {
        // This session has already seeded its working copy as English.
        $user = $this->actingAsContactUser('en');
        $this->get('r/contacts')->assertOk()->assertSee('lang="en"', false);

        // A second device switches the stored preference to Arabic.
        User::whereKey($user->id)->update(['locale' => 'ar']);

        // This session keeps English until its next sign-in (Q5=A).
        $this->get('r/contacts')->assertOk()->assertSee('lang="en"', false);
    }

    public function test_a_contactless_account_switches_only_the_session(): void
    {
        // The bare acting user from the harness has no linked contact.
        $user = auth()->user();

        $this->postAjax('locale', ['locale' => 'ar'])->assertRedirect();

        $this->assertSame('ar', session('locale'));
        $this->assertSame('en', User::whereKey($user->id)->value('locale'), 'the stored default stays untouched without a contact');
    }
}
