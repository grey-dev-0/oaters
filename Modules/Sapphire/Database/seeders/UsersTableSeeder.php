<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Sapphire\App\Models\Tenant;
use Modules\Sapphire\App\Models\User;

class UsersTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
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
        // Linking main system user to the tenant account.
        $user = User::whereRoleId(1)->first();
        Tenant::whereEmail('george@maxwell.com')->update(['user_id' => $user->id]);
        $user->contact->emails()->create([
            'address' => 'george@maxwell.com',
            'default' => true
        ]);
    }
}
