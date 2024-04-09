<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sapphire\Database\seeders\UsersTableSeeder;

class TenantAppSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $this->call(UsersTableSeeder::class);
    }
}
