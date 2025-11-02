<?php

namespace Database\Factories;

use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Species>
 */
class SpeciesFactory extends Factory
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
            'family_id' => Family::factory(),
            'name' => fake()->words(2, true),
            'scientific_name' => fake()->words(2, true),
            'size_category' => fake()->randomElement(['small', 'medium', 'large']),
            'color_description' => fake()->sentence(),
            'special_features' => fake()->sentence(),
            'gender_differences' => fake()->sentence(),
            'generations_per_year' => fake()->numberBetween(1, 3),
            'hibernation_stage' => fake()->randomElement(['egg', 'larva', 'pupa', 'adult']),
            'pupal_duration_days' => fake()->numberBetween(5, 300),
            'red_list_status_de' => fake()->randomElement(['0', '1', '2', '3', 'R', 'D', 'G']),
            'red_list_status_eu' => fake()->randomElement(['LC', 'NT', 'VU', 'EN', 'CR']),
            'abundance_trend' => fake()->randomElement(['increasing', 'stable', 'decreasing']),
            'protection_status' => fake()->randomElement(['none', 'protected', 'strictly_protected']),
        ];
    }
}
