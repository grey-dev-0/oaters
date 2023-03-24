<?php

namespace Modules\Sapphire\Database\Seeders;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Common\Entities\Contact;
use Modules\Sapphire\Entities\User;

class UsersTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        Model::unguard();

        $sequenceGenerator = function($sequence){
            $iteration = $sequence->index + 1;
            $role_id = match(true){
                $iteration <= 10 => $iteration,
                $iteration <= 20 => $iteration - 10,
                $iteration <= 25 => $iteration - 20
            };
            return compact('role_id');
        };
        User::factory()->state(new Sequence($sequenceGenerator))
            ->has(Contact::factory()->state(new Sequence($sequenceGenerator)))->count(25)->create();
    }
}
