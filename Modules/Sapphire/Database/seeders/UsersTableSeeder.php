<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Seeder;
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
        $roles = Role::orderBy('id')->get(['id', 'name', 'guard_name']);
        User::factory()->count(25)->create()->load('contact')
            ->each(fn($user, $i) => $user->assignRole($roles[$this->getRoleIndex($i)]));
        // Linking main system user to the tenant account.
        $user = User::role($roles[0]->id)->first();
        Tenant::whereEmail('george@maxwell.com')->update(['user_id' => $user->id]);
    }

    private function getRoleIndex($i){
        return match(true){
            $i < 10 => $i,
            $i < 20 => $i - 10,
            $i < 25 => $i - 20
        };
    }
}
