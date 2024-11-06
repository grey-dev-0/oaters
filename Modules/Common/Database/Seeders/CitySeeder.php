<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\City;

class CitySeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        if(file_exists($file = base_path('storage/app/cities.sql.gz'))){
            $queries = explode('insert into ', implode(gzfile($file)));
            foreach($queries as $query)
                if(!empty(trim($query)))
                    \DB::unprepared("insert into $query");
        } else
            foreach(['asia', 'africa', 'europe', 'north-america', 'central-america', 'south-america'] as $continent){
                if(!file_exists($dataFile = base_path("storage/app/$continent-cities.json")))
                    continue;
                $cities = json_decode(file_get_contents($dataFile), true);
                City::factory()->sequence(...$cities)->count(count($cities))->create();
            }
    }
}
