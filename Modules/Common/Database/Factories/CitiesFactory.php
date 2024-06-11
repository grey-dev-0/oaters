<?php

namespace Modules\Common\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Common\App\Models\Country;

class CitiesFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Common\App\Models\City::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array{
        return [
            'country_id' => Country::factory(),
            'en' => ['name' => 'Alexandria'],
            'ar' => ['name' => 'الاسكندرية']
        ];
    }
}

