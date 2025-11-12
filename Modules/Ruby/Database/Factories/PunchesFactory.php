<?php

namespace Modules\Ruby\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Common\App\Models\Contact;

class PunchesFactory extends Factory{
    protected $model = \Modules\Ruby\App\Models\Punch::class;

    public function definition(): array{
        return [
            'contact_id' => Contact::factory(),
            'type' => $this->faker->randomElement(['in', 'out']),
            'shift_id' => null,
            'latency' => null,
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function withShift(): self{
        return $this->state(function() {
            return [
                'shift_id' => rand(1, 4),
                'latency' => rand(-30, 60),
            ];
        });
    }

    public function inPunch(): self{
        return $this->state(fn() => ['type' => 'in']);
    }

    public function outPunch(): self{
        return $this->state(fn() => ['type' => 'out']);
    }
}
