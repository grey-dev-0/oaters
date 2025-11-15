<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Punch;

class AttendanceSeeder extends Seeder{
    public function run(): void{
        $contacts = Contact::all('id');
        $contacts->shift();
        $punches = [];

        $contacts->each(function($contact) use (&$punches) {
            for ($i = 0; $i < 20; $i++) {
                $shouldHaveShift = rand(0, 1);

                if ($shouldHaveShift) {
                    $punches[] = Punch::factory()->withShift()->inPunch()->make(['contact_id' => $contact->id])->getAttributes();
                    $punches[] = Punch::factory()->withShift()->outPunch()->make(['contact_id' => $contact->id])->getAttributes();
                } else {
                    $punches[] = Punch::factory()->inPunch()->make(['contact_id' => $contact->id])->getAttributes();
                    $punches[] = Punch::factory()->outPunch()->make(['contact_id' => $contact->id])->getAttributes();
                }
            }
        });
        collect($punches)->chunk(500)->each(fn($batch) => Punch::insert($batch->toArray()));
    }
}
