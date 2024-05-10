<?php

namespace Modules\Ruby\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Common\App\Models\Contact;
use Modules\Common\App\Models\Country;
use Modules\Ruby\App\Models\Degree;

class ApplicantsFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Ruby\App\Models\Applicant::class;

    /**
     * @var int[] Country IDs.
     */
    private static $countries;

    /**
     * @var int[] Educational degree IDs.
     */
    private static $degrees;

    /**
     * Define the model's default state.
     */
    public function definition(): array{
        return [
            'id' => Contact::factory(),
            'country_id' => (self::$countries ??= Country::pluck('id'))->random(),
            'degree_id' => (self::$degrees ??= Degree::pluck('id'))->random(),
            'degree_date' => $this->faker->year(now()->subYear()->format('Y')),
            'tenure' => $this->faker->randomNumber(2),
            'recruited_at' => (new \Carbon\Carbon($this->faker->dateTimeBetween('-15 years', '-5 years')))->toDateString()
        ];
    }
}

