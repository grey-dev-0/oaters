<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Seeder;
use Modules\Sapphire\App\Models\Role;

class CentralRoleSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $roles = [
            ['name' => 'master', 'guard_name' => 'admin', 'en' => ['title' => 'Landlord'], 'ar' => ['title' => 'مالك']],
            ['name' => 'tenant', 'guard_name' => 'admin', 'en' => ['title' => 'Tenant'], 'ar' => ['title' => 'مستأجر']]
        ];
        foreach($roles as $role)
            Role::create($role);
    }
}
