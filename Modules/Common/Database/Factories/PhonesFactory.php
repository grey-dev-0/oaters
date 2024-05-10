<?php

namespace Modules\Common\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhonesFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Common\App\Models\Phone::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array{
        return [
            'number' => $this->faker->phoneNumber(),
            'default' => false
        ];
    }
}

