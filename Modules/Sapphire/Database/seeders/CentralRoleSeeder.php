<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CentralRoleSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $roles = [
            ['name' => 'master', 'guard_name' => 'admin'],
            ['name' => 'tenant', 'guard_name' => 'admin']
        ];
        foreach($roles as $role)
            Role::create($role);
    }
}
