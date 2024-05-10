<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\Email;
use Modules\Common\App\Models\Phone;
use Modules\Sapphire\App\Models\User;

class ContactSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $users = User::with('roles')->get('id');
        Contact::factory()->sequence(fn($sequence) => ['user_id' => $users[$sequence->index]->id])
            ->has(Phone::factory()->state(['default' => true]))->has(Email::factory()->sequence(fn($sequence) => [
                'address' => ($sequence->index == 0 ? 'george@maxwell.com' : fake()->email()),
                'default' => true
            ]))->count($users->count())->create()->each(fn($contact, $i) => $contact->assignRole($users[$i]->roles));
    }
}
