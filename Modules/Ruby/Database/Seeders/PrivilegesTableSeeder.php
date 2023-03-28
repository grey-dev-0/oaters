<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Sapphire\Constants\Roles;
use Modules\Sapphire\Entities\Privilege;
use Modules\Sapphire\Entities\Role;

class PrivilegesTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Model::unguard();

        $roles = Role::whereIn('id', array_keys(\Arr::except(Roles::options(), [Roles::SUPPLIER, Roles::CUSTOMER])))->get();
        $privileges = collect([
            ['name' => 'ruby', 'en' => ['title' => 'Ruby Access'], 'ar' => ['title' => 'تصريح Ruby']],
            ['name' => 'view-personnel', 'en' => ['title' => 'List Employees'], 'ar' => ['title' => 'عرض الموظفين']],
            ['name' => 'view-payroll', 'en' => ['title' => 'View Payroll'], 'ar' => ['title' => 'عرض الرواتب']],
            ['name' => 'view-notices', 'en' => ['title' => 'View Notices'], 'ar' => ['title' => 'عرض إشعارات الموظفين']],
            ['name' => 'view-leaves', 'en' => ['title' => 'List Leaves'], 'ar' => ['title' => 'عرض الإجازات']],
            ['name' => 'view-attendance', 'en' => ['title' => 'View Attendance'], 'ar' => ['title' => 'عرض الحضور والانصراف']],
            ['name' => 'view-recruitment', 'en' => ['title' => 'View Recruitment'], 'ar' => ['title' => 'عرض طلبات التوظيف']],
        ]);
        $privileges->each(function($privilege) use($roles){
            $p = Privilege::create($privilege);
            if($p->name == 'ruby')
                $p->roles()->attach($roles);
        });
        
        $rolePrivileges = [
            Roles::OWNER => $privileges->where('name', '!=', 'ruby')->pluck('name')->toArray(),
            Roles::FINANCE_MANAGER => $privileges->whereNotIn('name', ['ruby', 'view-recruitment'])->pluck('name')->toArray(),
            Roles::HR_MANAGER => $privileges->where('name', '!=', 'ruby')->pluck('name')->toArray(),
            Roles::DIRECTOR => ['view-personnel', 'view-notices', 'view-leaves', 'view-attendance'],
            Roles::LEAD => ['view-personnel', 'view-notices', 'view-leaves', 'view-attendance'],
            Roles::ACCOUNTANT =>  $privileges->whereNotIn('name', ['ruby', 'view-recruitment'])->pluck('name')->toArray(),
            Roles::HR_AGENT => $privileges->where('name', '!=', 'ruby')->pluck('name')->toArray(),
            Roles::EMPLOYEE => ['view-notices', 'view-leaves', 'view-attendance'],
        ];
        $roles->each(function($role) use($rolePrivileges){
            $role->privileges()->attach(Privilege::whereIn('name', $rolePrivileges[$role->id])->pluck('id')->toArray());
        });
    }
}
