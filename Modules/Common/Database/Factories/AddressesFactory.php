<?php

namespace Modules\Common\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\City;

class AddressesFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Common\App\Models\Address::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array{
        return [
            'contact_id' => Contact::factory(),
            'city_id' => City::factory(),
            'line_1' => $this->faker->address(),
            'line_2' => $this->faker->streetAddress(),
            'lat' => $this->faker->latitude(-90, 90),
            'long' => $this->faker->longitude(-180, 180),
            'default' => false
        ];
    }
}

