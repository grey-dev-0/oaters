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
