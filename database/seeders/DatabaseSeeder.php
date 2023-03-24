<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sapphire\Database\Seeders\RolesTableSeeder;
use Modules\Sapphire\Database\Seeders\TenantsTableSeeder;
use Modules\Sapphire\Database\Seeders\UsersTableSeeder;

class DatabaseSeeder extends Seeder{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(){
        $this->call(ModuleSeeder::class);
        $this->call(TenantsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
