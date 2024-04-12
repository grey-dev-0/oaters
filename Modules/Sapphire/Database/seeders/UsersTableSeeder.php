<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Sapphire\App\Models\Tenant;
use Modules\Sapphire\App\Models\User;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $roles = Role::orderBy('id')->get(['id', 'guard_name']);
        User::factory()->has(Contact::factory())->count(25)->create()->load('contact')
            ->each(function($user, $i) use($roles){
                $i = $this->getRoleIndex($i);
                $user->assignRole($roles[$i])->contact->assignRole($roles[$i]);
            });
        // Linking main system user to the tenant account.
        $user = User::role($roles[0]->id)->first();
        Tenant::whereEmail('george@maxwell.com')->update(['user_id' => $user->id]);
        $user->contact->emails()->create([
            'address' => 'george@maxwell.com',
            'default' => true
        ]);
    }

    private function getRoleIndex($i){
        return match(true){
            $i < 10 => $i,
            $i < 20 => $i - 10,
            $i < 25 => $i - 20
        };
    }
}
