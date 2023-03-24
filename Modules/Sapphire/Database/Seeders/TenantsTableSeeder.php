<?php

namespace Modules\Sapphire\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Sapphire\Entities\Module;
use Modules\Sapphire\Entities\Tenant;

class TenantsTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Model::unguard();

        Tenant::create([
            'name' => 'Mohyaddin Saleh',
            'email' => 'mo7y.66@gmail.com',
            'password' => \Hash::make('test123'),
            'hash' => sha1(\Str::random().time()),
            'main' => true
        ]);

        Tenant::create([
            'name' => 'George Maxwell',
            'email' => 'george@maxwell.com',
            'password' => \Hash::make('test123'),
            'hash' => sha1(\Str::random().time()),
            'subdomain' => 'maxwell'
        ])->subscriptions()->create([
            'price' => 55.55,
            'paid' => true,
            'expires_at' => now()->addYear()
        ])->modules()->attach(Module::pluck('id')->toArray());
    }
}
