<?php

namespace Modules\Sapphire\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Sapphire\Entities\User;

class UsersTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Model::unguard();

        User::factory()->state([
            'role_id' => mt_rand(1, 5)
        ])->count(25)->create();
    }
}
