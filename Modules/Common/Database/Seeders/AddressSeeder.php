<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Address;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\City;

class AddressSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $contacts = Contact::all('id');
        $cities = City::all('id');
        $count = $contacts->count();
        Address::factory()->sequence(fn($sequence) => [
            'contact_id' => $contacts[$sequence->index % $count]->id,
            'default' => $sequence->index % 3 === 0,
        ])->recycle($cities)->count($count * 3)->create();
    }
}
