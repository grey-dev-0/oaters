<?php

namespace Modules\Common\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CountriesFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Common\App\Models\Country::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array{
        return [
            'code' => 'EGP',
            'en' => ['name' => 'Egypt'],
            'ar' => ['name' => 'مصر']
        ];
    }
}

