<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ruby\App\Models\Department;
use Modules\Sapphire\App\Models\User;

class DepartmentSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $departments = [
            ['en' => ['name' => 'Human Resources'], 'ar' => ['name' => 'الموارد البشرية']],
            ['en' => ['name' => 'Finance'], 'ar' => ['name' => 'التمويل']],
            ['en' => ['name' => 'Marketing'], 'ar' => ['name' => 'التسويق']],
            ['en' => ['name' => 'Sales'], 'ar' => ['name' => 'المبيعات']],
            ['en' => ['name' => 'Operations'], 'ar' => ['name' => 'التشغيل']],
            ['en' => ['name' => 'Information Technology'], 'ar' => ['name' => 'تقنية المعلومات']],
            ['en' => ['name' => 'Production'], 'ar' => ['name' => 'الانتاج']]
        ];

        foreach($departments as $department)
            Department::create($department);

        $users = User::with('roles')->get('id')
            ->each(fn($user) => $user->setAttribute('role', $user->roles[0]->name));
        Department::with(['translations' => fn($locales) => $locales->whereLocale('en')])
            ->get()->each(function($department) use ($users){
                [$manager_id, $contacts] = match($department->{'name:en'}){
                    'Human Resources' => [
                        $users->where('role', 'hr-manager')->first()->id,
                        $users->where('role', 'hr-assistant')->pluck('id')
                    ],
                    'Finance' => [
                        $users->where('role', 'financial-manager')->first()->id,
                        $users->where('role', 'accountant')->pluck('id')
                    ],
                    'Information Technology' => [
                        $users->where('role', 'director')->first()->id,
                        $users->where('role', 'employee')->pluck('id')
                    ],
                    default => [null, null]
                };
                if(is_null($manager_id) || is_null($contacts))
                    return;
                $department->employees()->attach($contacts, compact('manager_id'));
            });
    }
}
