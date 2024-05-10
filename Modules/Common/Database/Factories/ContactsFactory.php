<?php

namespace Modules\Common\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Sapphire\App\Models\User;

class ContactsFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Common\App\Models\Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(){
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'job'=> $this->faker->jobTitle(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birthdate' => $this->faker->date('Y-m-d', now()->subYears(18)->toDateString())
        ];
    }
}

