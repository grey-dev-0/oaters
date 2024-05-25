<?php

namespace Modules\Ruby\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Common\App\Models\Contact;

class LeavesFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Ruby\App\Models\Leave::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array{
        $starts_at = $this->faker->date('Y-m-d', '+6 months');
        $units = $this->faker->numberBetween(2, 12);
        $addMethod = $this->faker->randomElement(['addDays', 'addMonths']);
        $ends_at = (new \Carbon\Carbon($starts_at))->$addMethod($units)->toDateString();
        return [
            'contact_id' => Contact::factory(),
            'type' => $this->faker->randomElement(['sick', 'casual', 'annual']),
            'status' => $this->faker->boolean(),
        ] + compact('starts_at', 'ends_at');
    }
}

