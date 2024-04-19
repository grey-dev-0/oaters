<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ruby\App\Models\Department;

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
    }
}
