<?php

namespace Tests\Feature\Ruby;

use Modules\Common\App\Models\City;
use Modules\Common\App\Models\CityLocale;
use Modules\Common\App\Models\Contact;

class ContactsPageTest extends StaffTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The module layout's navbar renders the signed-in user's contact.
        auth()->user()->contact()->save(Contact::create(['name' => 'Contacts Tester']));
    }

    public function test_contacts_page_renders_the_roles_select_and_default_radios(): void
    {
        $response = $this->get('r/contacts');

        $response->assertOk();
        $content = $response->getContent();
        $this->assertSame(2, substr_count($content, 'name="roles[]"'), 'both staff form modals carry a roles select');
        $this->assertStringContainsString('id="contact-roles"', $content);
        $this->assertStringContainsString('id="e_contact-roles"', $content);
        // The roles select sits right after the Job Title field of the contact pane.
        $this->assertGreaterThan(strpos($content, 'name="job"'), strpos($content, 'id="contact-roles"'));
        // Defaults are labeled radios carrying explicit 1/0 values, not bare checkboxes.
        $this->assertSame(0, substr_count($content, 'type="checkbox"'), 'no checkboxes left for defaults');
        $this->assertStringContainsString("type=\"radio\"", $content);
        $this->assertStringContainsString("email.default ? 1 : 0", $content);
        $this->assertStringContainsString("phone.default ? 1 : 0", $content);
        $this->assertStringContainsString(trans('ruby::contacts.default'), $content, 'localized default label');
    }

    public function test_contacts_page_does_not_inline_the_city_catalog(): void
    {
        foreach (range(1, 30) as $i) {
            $city = City::create(['country_id' => $this->makeCountry()->id]);
            CityLocale::create(['city_id' => $city->id, 'locale' => 'en', 'name' => "Catalog City $i"]);
        }

        $response = $this->get('r/contacts');

        $response->assertOk();
        $this->assertStringNotContainsString('Catalog City 1', $response->getContent(),
            'cities must not be shipped as inline options');
        $response->assertSee('citiesUrl', false);
    }

    public function test_city_search_endpoint_returns_matching_cities_only(): void
    {
        $city = $this->makeCity(); // "Test City"
        CityLocale::create(['city_id' => $city->id, 'locale' => 'ar', 'name' => 'مدينة الاختبار']);

        $response = $this->postAjax('r/contacts/cities', ['query' => 'Test', 'limit' => 5]);

        $response->assertOk();
        $suggestions = $response->json('suggestions');
        $this->assertSame(['Test City'], array_values($suggestions));
        $this->assertSame([$city->id], array_map('intval', array_keys($suggestions)));
    }

    public function test_city_search_endpoint_is_limited(): void
    {
        foreach (range(1, 20) as $i) {
            $city = City::create(['country_id' => $this->makeCountry()->id]);
            CityLocale::create(['city_id' => $city->id, 'locale' => 'en', 'name' => "Limited City $i"]);
        }

        $response = $this->postAjax('r/contacts/cities', ['query' => 'Limited', 'limit' => 5]);

        $response->assertOk();
        $this->assertCount(5, $response->json('suggestions'));
    }
}
