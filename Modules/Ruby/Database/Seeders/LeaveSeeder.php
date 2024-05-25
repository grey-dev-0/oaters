<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Leave;

class LeaveSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $contacts = Contact::all('id');
        $contacts->tap(fn($collection) => $collection->shift())->values();
        $count = $contacts->count();
        Leave::factory()->sequence(fn($sequence) => ['contact_id' => $contacts[$sequence->index % $count]->id])
            ->count($count * 5)->create();
    }
}
