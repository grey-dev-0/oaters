<?php

namespace Modules\Common\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MeasurementUnitsFactory extends Factory{
    protected $model = \Modules\Common\App\Models\MeasurementUnit::class;

    public function definition(): array{
        return [
            'type' => $this->faker->randomElement(['length', 'area', 'volume', 'weight', 'data', 'box', 'piece']),
            'base_id' => null,
            'factor' => 1,
            'custom' => false,
            'en' => ['name' => 'Unit', 'symbol' => 'U'],
            'ar' => ['name' => 'وحدة', 'symbol' => 'و'],
        ];
    }

    public function asBaseUnit(string $type, string $nameEn, string $symbolEn, string $nameAr, string $symbolAr): self
    {
        return $this->state([
            'type' => $type,
            'base_id' => null,
            'factor' => 1,
            'en' => ['name' => $nameEn, 'symbol' => $symbolEn],
            'ar' => ['name' => $nameAr, 'symbol' => $symbolAr],
        ]);
    }

    public function asDerivedUnit(?int $baseId, string $type, float $factor, string $nameEn, string $symbolEn, string $nameAr, string $symbolAr): self
    {
        return $this->state([
            'type' => $type,
            'base_id' => $baseId,
            'factor' => $factor,
            'en' => ['name' => $nameEn, 'symbol' => $symbolEn],
            'ar' => ['name' => $nameAr, 'symbol' => $symbolAr],
        ]);
    }
}
