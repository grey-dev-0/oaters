<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Seeder;
use Modules\Sapphire\App\Models\Role;

class RoleSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $roles = [
            ['name' => 'general-manager', 'en' => ['title' => 'General Manager'], 'ar' => ['title' => 'مدير عام']],
            ['name' => 'financial-manager', 'en' => ['title' => 'Financial Manager'], 'ar' => ['title' => 'مدير مالي']],
            ['name' => 'hr-manager', 'en' => ['title' => 'HR Manager'], 'ar' => ['title' => 'مدير موارد بشرية']],
            ['name' => 'director', 'en' => ['title' => 'Director'], 'ar' => ['title' => 'مشرف']],
            ['name' => 'team-lead', 'en' => ['title' => 'Team Lead'], 'ar' => ['title' => 'قائد الفريق']],
            ['name' => 'accountant', 'en' => ['title' => 'Accountant'], 'ar' => ['title' => 'محاسب']],
            ['name' => 'hr-assistant', 'en' => ['title' => 'HR Assistant'], 'ar' => ['title' => 'مساعد موارد بشرية']],
            ['name' => 'employee', 'en' => ['title' => 'Employee'], 'ar' => ['title' => 'موظف']],
            ['name' => 'supplier', 'en' => ['title' => 'Supplier'], 'ar' => ['title' => 'موّرد']],
            ['name' => 'customer', 'en' => ['title' => 'Customer'], 'ar' => ['title' => 'عميل']]
        ];
        foreach($roles as $role)
            Role::create($role);
    }
}
