<?php

namespace Modules\Ruby\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftsFactory extends Factory{
    protected $model = \Modules\Ruby\App\Models\Shift::class;

    public function definition(): array{
        return [
            'start' => $this->faker->time('H:i'),
            'end' => $this->faker->time('H:i'),
        ];
    }

    public function morning(): self{
        return $this->state(fn() => [
            'start' => '09:00',
            'end' => '17:00',
        ]);
    }

    public function midday(): self{
        return $this->state(fn() => [
            'start' => '12:00',
            'end' => '20:00',
        ]);
    }

    public function night(): self{
        return $this->state(fn() => [
            'start' => '20:00',
            'end' => '04:00',
        ]);
    }
}
