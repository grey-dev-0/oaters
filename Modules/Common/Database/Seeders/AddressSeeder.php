<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Address;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\Country;

class AddressSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $contacts = Contact::all('id');
        $countries = Country::all('id');
        $count = $contacts->count();
        Address::factory()->sequence(fn($sequence) => [
            'contact_id' => $contacts[$sequence->index % $count]->id,
            'default' => $sequence->index % 3 === 0,
        ])->recycle($countries)->count($count * 3)->create();
    }
}
