<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ruby\Database\Seeders\PermissionSeeder as RubyPermissionSeeder;
use Modules\Sapphire\Database\seeders\RoleSeeder;
use Modules\Sapphire\Database\seeders\UsersTableSeeder;

class TenantAppSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $this->call(RoleSeeder::class);
        $this->call(RubyPermissionSeeder::class);
        if(!app()->isProduction())
            $this->call(UsersTableSeeder::class);
    }
}
