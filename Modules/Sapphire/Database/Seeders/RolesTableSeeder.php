<?php

namespace Modules\Sapphire\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Sapphire\Entities\Role;

class RolesTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Model::unguard();

        $roles = [
            ['en' => ['title' => 'General Manager'], 'ar' => ['title' => 'مدير عام']],
            ['en' => ['title' => 'Financial Manager'], 'ar' => ['title' => 'مدير مالي']],
            ['en' => ['title' => 'HR Manager'], 'ar' => ['title' => 'مدير موارد بشرية']],
            ['en' => ['title' => 'Director'], 'ar' => ['title' => 'مشرف']],
            ['en' => ['title' => 'Team Lead'], 'ar' => ['title' => 'قائد فريق']],
            ['en' => ['title' => 'Accountant'], 'ar' => ['title' => 'محاسب']],
            ['en' => ['title' => 'HR Assistant'], 'ar' => ['title' => 'مساعد موارد بشرية']],
            ['en' => ['title' => 'Employee'], 'ar' => ['title' => 'موظف']],
            ['en' => ['title' => 'Supplier'], 'ar' => ['title' => 'موّرد']],
            ['en' => ['title' => 'Customer'], 'ar' => ['title' => 'زبون']]
        ];

        foreach($roles as $role)
            Role::create($role);
    }
}
