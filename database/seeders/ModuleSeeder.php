<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sapphire\Entities\Module;

class ModuleSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Module::whereRaw('true = true')->delete();
        \DB::table('modules')->insert([
            ['name' => 'onyx', 'price' => 99.99],
            ['name' => 'amethyst', 'price' => 29.99],
            ['name' => 'topaz', 'price' => 9.99],
            ['name' => 'emerald', 'price' => 49.99],
            ['name' => 'ruby', 'price' => 34.99],
            ['name' => 'sapphire', 'price' => 4.99]
        ]);
    }
}
