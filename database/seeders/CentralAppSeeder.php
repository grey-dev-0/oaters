<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sapphire\Database\seeders\CentralRoleSeeder;
use Modules\Sapphire\Database\seeders\ModuleSeeder;
use Modules\Sapphire\Database\seeders\TenantsTableSeeder;

class CentralAppSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $this->call(ModuleSeeder::class);
        $this->call(CentralRoleSeeder::class);
        $this->call(TenantsTableSeeder::class);
    }
}
