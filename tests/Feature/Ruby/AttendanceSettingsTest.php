<?php

namespace Tests\Feature\Ruby;

use Modules\Ruby\App\Support\Punctuality;

class AttendanceSettingsTest extends AttendanceTestCase
{
    private function getSettings()
    {
        return $this->get('r/attendance/settings', [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
    }

    public function test_returns_default_threshold_when_never_configured(): void
    {
        $this->grantSettingsPermission();

        $this->getSettings()->assertOk()->assertJson(['threshold' => 15]);
    }

    public function test_persists_threshold_change(): void
    {
        $this->grantSettingsPermission();

        $this->postAjax('r/attendance/settings', ['threshold' => 5])->assertOk();

        $this->assertSame('5', \Settings::get(Punctuality::KEY));
        $this->getSettings()->assertOk()->assertJson(['threshold' => 5]);
    }

    public function test_rejects_invalid_thresholds_without_changing_stored_value(): void
    {
        $this->grantSettingsPermission();
        \Settings::set(Punctuality::KEY, 20);

        foreach ([[], ['threshold' => ''], ['threshold' => 'abc'], ['threshold' => -1], ['threshold' => 241], ['threshold' => 2.5]] as $payload) {
            $this->postAjax('r/attendance/settings', $payload)
                ->assertStatus(422)
                ->assertJsonValidationErrors('threshold');
        }

        $this->assertSame('20', \Settings::get(Punctuality::KEY));
    }

    public function test_localizes_validation_messages_to_arabic(): void
    {
        $this->grantSettingsPermission();
        app()->setLocale('ar');

        $response = $this->postAjax('r/attendance/settings', ['threshold' => 241]);

        $response->assertStatus(422);
        $this->assertStringContainsString('دقيقة', $response->json('errors.threshold.0'));
    }

    public function test_denies_users_without_the_settings_permission(): void
    {
        $this->actingAsTeamLead();

        $this->getSettings()->assertForbidden();
        $this->postAjax('r/attendance/settings', ['threshold' => 5])->assertForbidden();

        $this->assertDatabaseMissing('lc_settings', ['key' => Punctuality::KEY]);
    }
}
