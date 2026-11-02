<?php

namespace Tests\Feature\Ruby;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Common\App\Models\City;
use Modules\Common\App\Models\CityLocale;
use Modules\Common\App\Models\Country;
use Modules\Common\App\Models\CountryLocale;
use Modules\Ruby\App\Models\Degree;
use Modules\Sapphire\App\Models\Role;
use Modules\Sapphire\App\Models\User;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Tests\TestCase;

class StaffTestCase extends TestCase
{
    use DatabaseTransactions;

    /**
     * Prepares the module schema once per test class, outside any test
     * transaction. Central migrations (database/migrations) are skipped:
     * they duplicate the permission tables owned by the Sapphire module
     * (central vs tenant schema overlap), and this tenancy-bypassed suite
     * exercises the module schema only.
     */
    public static function setUpBeforeClass(): void
    {
        $app = require dirname(__DIR__, 3).'/bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        $kernel->call('migrate:fresh', ['--path' => 'tests/fixtures/migrations', '--force' => true]);
        $kernel->call('module:migrate', ['--force' => true]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([InitializeTenancyBySubdomain::class, PreventAccessFromCentralDomains::class]);
        $this->actingAsUser();
    }

    /**
     * Posts like the staff form does (AJAX), so validation failures come
     * back as 422 JSON instead of redirect responses.
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function postAjax(string $uri, array $data = [])
    {
        return $this->post($uri, $data, [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Signs in a user allowed to access Ruby.
     */
    protected function actingAsUser(): User
    {
        $permission = \Spatie\Permission\Models\Permission::findOrCreate('ruby');
        $role = Role::findOrCreate('hr-manager');
        $role->givePermissionTo($permission);

        $user = User::create(['username' => 'tester', 'password' => 'secret-password', 'active' => true]);
        $user->assignRole($role);
        $this->actingAs($user);

        return $user;
    }

    /**
     * Creates a country usable as a nationality.
     */
    protected function makeCountry(): Country
    {
        $country = Country::create();
        CountryLocale::create(['country_id' => $country->id, 'locale' => 'en', 'name' => 'Testland']);

        return $country;
    }

    /**
     * Creates an educational degree.
     */
    protected function makeDegree(): Degree
    {
        return Degree::create();
    }

    /**
     * Creates a city usable in addresses.
     */
    protected function makeCity(): City
    {
        $city = City::create(['country_id' => $this->makeCountry()->id]);
        CityLocale::create(['city_id' => $city->id, 'locale' => 'en', 'name' => 'Test City']);

        return $city;
    }

    /**
     * Builds a complete, valid staff member creation payload.
     */
    protected function memberPayload(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Jane Doe',
            'job' => 'Accountant',
            'emails' => [
                ['address' => 'jane@example.com', 'default' => '1'],
                ['address' => 'jane.d@example.com'],
            ],
            'phones' => [
                ['number' => '0123456789', 'default' => '1'],
                ['number' => '0987654321'],
            ],
            'addresses' => [
                ['city_id' => $this->makeCity()->id, 'line_1' => 'Street 1', 'line_2' => 'Apt 2'],
            ],
            'roles' => [$this->hrRole()->id],
            'member_departments' => [],
            'managed_departments' => [],
            'applicant_mode' => 'new',
            'nationality' => $this->makeCountry()->id,
            'degree' => $this->makeDegree()->id,
            'degree_date' => 2015,
            'recruited_at' => '2026-01-05',
            'username' => 'janedoe',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ];
    }

    /**
     * Gets (or creates) the HR role.
     */
    protected function hrRole(): Role
    {
        return Role::findOrCreate('hr-manager');
    }

    /**
     * Fakes the local disk with a sample employment document upload.
     */
    protected function sampleDocument(): UploadedFile
    {
        Storage::fake('local');

        return UploadedFile::fake()->create('credentials.pdf', 100, 'application/pdf');
    }
}
