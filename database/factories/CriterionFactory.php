<?php

namespace Database\Factories;

use App\Enums\Criterion\ImpactType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Criterion>
 */
class CriterionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'weight' => Arr::random(rand(1, 9), null),
            'impact_type' => Arr::random(ImpactType::Cost, ImpactType::Benefit),
        ];
    }
}
