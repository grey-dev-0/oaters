<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ruby\App\Models\Shift;

class ShiftSeeder extends Seeder{
    public function run(): void{
        Shift::factory()->morning()->create();
        Shift::factory()->midday()->create();
        Shift::factory()->night()->create();
        Shift::factory()->create(['start' => '08:00', 'end' => '16:00']);
    }
}
