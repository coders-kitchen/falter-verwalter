<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Family>
 */
class FamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'subfamily' => fake()->optional()->word(),
            'genus' => fake()->optional()->word(),
            'tribe' => fake()->optional()->word(),
            'type' => fake()->randomElement(['butterfly', 'plant']),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
