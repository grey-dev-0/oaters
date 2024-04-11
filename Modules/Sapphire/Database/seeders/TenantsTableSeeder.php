<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Seeder;
use Modules\Sapphire\App\Models\Module;
use Modules\Sapphire\App\Models\Tenant;

class TenantsTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Tenant::create([
            'name' => 'Mohyaddin Saleh',
            'email' => 'mo7y.66@gmail.com',
            'password' => \Hash::make('test123'),
            'hash' => sha1(\Str::random().time()),
            'tenancy_create_database' => false
        ])->assignRole('master');

        Tenant::create([
            'name' => 'George Maxwell',
            'email' => 'george@maxwell.com',
            'password' => \Hash::make('test123'),
            'hash' => 'test321',
            'tenancy_db_name' => 'maxwell'
        ])->assignRole('tenant')->tap(fn($tenant) => $tenant->domains()->create(['domain' => 'maxwell']))->subscriptions()->create([
            'price' => 55.55,
            'paid' => true,
            'expires_at' => now()->addYear()
        ])->modules()->attach(Module::pluck('id')->toArray());
    }
}
