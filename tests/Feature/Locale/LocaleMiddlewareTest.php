<?php

namespace Tests\Feature\Locale;

use Modules\Sapphire\App\Models\User;

class LocaleMiddlewareTest extends LocaleTestCase
{
    public function test_the_session_working_copy_wins_over_the_account_preference(): void
    {
        $this->actingAsContactUser('en');
        session(['locale' => 'ar']);

        $this->get('r/contacts')->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false);
    }

    public function test_the_first_authenticated_request_seeds_the_session_from_the_account(): void
    {
        $this->actingAsContactUser('ar');

        $this->get('r/contacts')->assertOk()->assertSee('lang="ar"', false);

        $this->assertSame('ar', session('locale'), 'the seeded value must be cached in the session');
    }

    public function test_the_account_is_read_only_while_the_session_has_no_preference(): void
    {
        $user = $this->actingAsContactUser('ar');

        $this->get('r/contacts')->assertOk()->assertSee('lang="ar"', false);

        // Another device switches the stored preference to English.
        User::whereKey($user->id)->update(['locale' => 'en']);

        // This live session keeps its working copy until the next sign-in (Q5=A).
        $this->get('r/contacts')->assertOk()->assertSee('lang="ar"', false);
    }

    public function test_english_uses_left_to_right_direction(): void
    {
        $this->actingAsContactUser('en');

        $this->get('r/contacts')->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('dir="ltr"', false);
    }

    public function test_anonymous_requests_default_to_english(): void
    {
        auth()->logout();

        $this->get('r/login')->assertOk()->assertSee('lang="en"', false);
    }

    public function test_signing_out_clears_the_working_copy_so_login_stays_english(): void
    {
        $this->actingAsContactUser('ar');
        $this->get('r/contacts')->assertOk()->assertSee('lang="ar"', false);

        $this->get('s/logout');
        $this->assertGuest();

        $this->get('r/login')->assertOk()->assertSee('lang="en"', false);
    }

    public function test_a_fresh_sign_in_adopts_the_account_preference(): void
    {
        $user = $this->actingAsContactUser('ar');
        auth()->logout();

        $this->post('r/login', ['username' => $user->username, 'password' => 'secret-password'])->assertRedirect('r');

        $this->get('r/contacts')->assertOk()->assertSee('lang="ar"', false);
    }
}
