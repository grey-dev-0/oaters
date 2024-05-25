<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\Phone;

class PhoneSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $contacts = Contact::all('id');
        $count = $contacts->count();
        Phone::factory()->sequence(fn($sequence) => ['contact_id' => $contacts[$sequence->index % $count]->id])
            ->count($count * 4)->create();
    }
}
