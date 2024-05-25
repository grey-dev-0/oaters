<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\Country;
use Modules\Ruby\App\Models\Applicant;

class ApplicantSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $contacts = Contact::all('id');
        $contacts->tap(fn($collection) => $collection->shift())->values();
        $countries = Country::all('id');
        Applicant::factory()->sequence(fn($sequence) => ['id' => $contacts[$sequence->index]->id])
            ->recycle($countries)->count($contacts->count())->create();
    }
}
