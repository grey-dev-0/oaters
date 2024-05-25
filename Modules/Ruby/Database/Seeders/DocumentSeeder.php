<?php

namespace Modules\Ruby\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ruby\App\Models\Applicant;
use Modules\Ruby\App\Models\Document;

class DocumentSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $applicants = Applicant::all('id');
        $count = $applicants->count();
        $example = \Storage::disk('local')->path('r_documents');
        if(!file_exists("$example/example.txt")){
            if(!file_exists($example))
                mkdir($example, 0777, true);
            file_put_contents("$example/example.txt", 'Private Example Document');
        }
        Document::factory()->sequence(fn($sequence) => ['applicant_id' => $applicants[$sequence->index % $count]->id])
            ->count($count * 5)->create();
    }
}
