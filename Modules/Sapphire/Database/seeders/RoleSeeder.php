<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $roles = [
            ['name' => 'general-manager'],
            ['name' => 'financial-manager'],
            ['name' => 'hr-manager'],
            ['name' => 'director'],
            ['name' => 'team-lead'],
            ['name' => 'accountant'],
            ['name' => 'hr-assistant'],
            ['name' => 'employee'],
            ['name' => 'supplier'],
            ['name' => 'customer']
        ];
        foreach($roles as $role)
            Role::create($role);
    }
}
