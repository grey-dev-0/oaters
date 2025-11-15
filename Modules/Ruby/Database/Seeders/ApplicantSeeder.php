<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\Country;
use Modules\Ruby\App\Models\Applicant;
use Modules\Ruby\App\Models\Shift;

class ApplicantSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $contacts = Contact::all('id');
        $contacts->shift();
        $countries = Country::all('id');
        $shifts = Shift::all('id');

        Applicant::factory()->sequence(fn($sequence) => ['id' => $contacts[$sequence->index]->id])
            ->recycle($countries)->count($contacts->count())->create()->load('contact:id')->each(function($applicant) use ($shifts){
                foreach($shifts as $shift){
                    $weekday = rand(0, 6);
                    $applicant->contact->shifts()->attach($shift->id, ['weekday' => $weekday]);
                }
            });
    }
}
