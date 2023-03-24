<?php

namespace Modules\Sapphire\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UsersFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Sapphire\Entities\User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(){
        return [
            'username' => $this->faker->userName(),
            'password' => \Hash::make('test123')
        ];
    }
}

