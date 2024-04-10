<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $permissions = collect([
            ['name' => 'ruby', 'guard_name' => 'web'],
            ['name' => 'view-personnel', 'guard_name' => 'web'],
            ['name' => 'view-payroll', 'guard_name' => 'web'],
            ['name' => 'view-notices', 'guard_name' => 'web'],
            ['name' => 'view-leaves', 'guard_name' => 'web'],
            ['name' => 'view-attendance', 'guard_name' => 'web'],
            ['name' => 'view-recruitment', 'guard_name' => 'web']
        ])->map(fn($permission) => Permission::create($permission));

        // Getting all roles involved with ruby module.
        $roles = Role::whereNotIn('name', ['supplier', 'customer'])->get();
        // Authorizing all involved roles to access ruby module.
        $permissions->first()->roles()->attach($roles);

        // Authorizing corresponding roles to their relevant permissions.
        $rolePermissions = [
            'general-manager' => $permissions->where('name', '!=', 'ruby'),
            'financial-manager' => $permissions->whereNotIn('name', ['ruby', 'view-recruitment']),
            'hr-manager' => $permissions->where('name', '!=', 'ruby'),
            'director' => $permissions->whereIn('name', ['view-personnel', 'view-notices', 'view-leaves', 'view-attendance']),
            'team-lead' => $permissions->whereIn('name', ['view-personnel', 'view-notices', 'view-leaves', 'view-attendance']),
            'accountant' =>  $permissions->whereNotIn('name', ['ruby', 'view-recruitment']),
            'hr-assistant' => $permissions->where('name', '!=', 'ruby'),
            'employee' => $permissions->whereIn('name', ['view-notices', 'view-leaves', 'view-attendance'])
        ];
        $roles->each(function($role) use($rolePermissions){
            $role->permissions()->attach($rolePermissions[$role->name]);
        });
    }
}
