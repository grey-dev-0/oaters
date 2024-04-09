<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sapphire\Database\seeders\TenantsTableSeeder;

class CentralAppSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $this->call(ModuleSeeder::class);
        $this->call(TenantsTableSeeder::class);
    }
}
