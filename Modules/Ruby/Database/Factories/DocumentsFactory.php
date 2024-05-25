<?php

namespace Modules\Ruby\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Ruby\App\Models\Applicant;

class DocumentsFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Ruby\App\Models\Document::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array{
        return [
            'applicant_id' => Applicant::factory(),
            'title' => $this->faker->sentence(3),
            'filename' => 'example.txt'
        ];
    }
}

