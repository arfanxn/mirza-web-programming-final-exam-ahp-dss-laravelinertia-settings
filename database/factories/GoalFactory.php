<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goals>
 */
class GoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attrs = [
            'title' => $this->faker->title(),
            'description' => rand(0, 1) ? $this->faker->text() : null,
        ];
        $attrs['slug'] = Str::slug(Str::lower($attrs['title']));
        return $attrs;
    }
}
