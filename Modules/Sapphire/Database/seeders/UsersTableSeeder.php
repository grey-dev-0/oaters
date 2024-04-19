<?php

namespace Modules\Sapphire\Database\seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Department;
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
        $users = User::factory()->has(Contact::factory())->count(25)->create()->load('contact')
            ->each(function($user, $i) use($roles){
                $i = $this->getRoleIndex($i);
                $user->assignRole($roles[$i])->contact->assignRole($roles[$i]);
                $user->setAttribute('role', $roles[$i]->name);
            });
        // Linking main system user to the tenant account.
        $user = User::role($roles[0]->id)->first();
        Tenant::whereEmail('george@maxwell.com')->update(['user_id' => $user->id]);
        $user->contact->emails()->create([
            'address' => 'george@maxwell.com',
            'default' => true
        ]);
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

    private function getRoleIndex($i){
        return match(true){
            $i < 10 => $i,
            $i < 20 => $i - 10,
            $i < 25 => $i - 20
        };
    }
}
