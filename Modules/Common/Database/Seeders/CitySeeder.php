<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\City;

class CitySeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        foreach(['asia', 'africa', 'europe', 'north-america', 'central-america', 'south-america'] as $continent){
            if(!file_exists($dataFile = base_path("storage/app/$continent-cities.json")))
                continue;
            $cities = json_decode(file_get_contents($dataFile), true);
            City::factory()->sequence(...$cities)->count(count($cities))->create();
        }
    }
}
