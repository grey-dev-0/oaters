<?php

namespace Modules\Common\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\Country;

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
            'country_id' => Country::factory(),
            'default' => false
        ];
    }
}

