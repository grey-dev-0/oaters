<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ruby\App\Models\Degree;

class DegreeSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $degrees = [
            ['en' => ['name' => 'Doctorate'], 'ar' => ['name' => 'دكتوراه']],
            ['en' => ['name' => 'Master'], 'ar' => ['name' => 'ماجستير']],
            ['en' => ['name' => 'Bachelor'], 'ar' => ['name' => 'بكالوريوس']],
            ['en' => ['name' => 'Diploma'], 'ar' => ['name' => 'دبلوم']],
            ['en' => ['name' => 'High School'], 'ar' => ['name' => 'ثانوية']],
            ['en' => ['name' => 'Mid School'], 'ar' => ['name' => 'متوسط']]
        ];

        foreach($degrees as $degree)
            Degree::create($degree);
    }
}
