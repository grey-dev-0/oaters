<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Country;

class CountrySeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $countries = json_decode(file_get_contents(base_path('storage/app/countries.json')), true);
        Country::factory()->sequence(...$countries)->count(count($countries))->create();
    }
}
