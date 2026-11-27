<?php

namespace Tests\Feature\Locale;

use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Applicant;
use Modules\Ruby\App\Models\Degree;
use Modules\Ruby\App\Models\DegreeLocale;

class LocaleFallbackTest extends LocaleTestCase
{
    public function test_core_validation_messages_render_in_arabic(): void
    {
        session(['locale' => 'ar']);

        $response = $this->postAjax('r/contacts/create', []);

        $response->assertStatus(422);
        $this->assertStringContainsString('مطلوب', $response->json('errors.name.0'));
        $this->assertStringContainsString('الاسم', $response->json('errors.name.0'), 'the attribute name must localize too');
    }

    public function test_profile_degree_falls_back_to_english_under_arabic(): void
    {
        session(['locale' => 'ar']);

        $degree = Degree::create();
        DegreeLocale::create(['degree_id' => $degree->id, 'locale' => 'en', 'name' => 'Fallback Degree']);

        $contact = Contact::factory()->create(['name' => 'Arabic Profile']);
        $contact->applicant()->create([
            'country_id' => $this->makeCountry()->id,
            'degree_id' => $degree->id,
            'degree_date' => 2015,
            'recruited_at' => '2026-01-05 00:00:00',
        ]);

        // The profile surface renders applicant.degree.name; with no Arabic
        // row it must fall back to the English value, not render empty.
        $this->get("r/contacts/{$contact->id}")
            ->assertOk()
            ->assertJsonPath('applicant.degree.name', 'Fallback Degree');
    }
}
