<?php

namespace Database\Factories;

use App\Enums\SurvivorGender;
use App\Enums\SurvivorStatus;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurvivorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'age' => $this->faker->numberBetween(10, 85),
            'last_location' => new Point($this->faker->randomFloat(min: -90, max: 90), $this->faker->randomFloat(min: -180, max: 180)),
            'status' => SurvivorStatus::CLEAN
        ];
    }
}
