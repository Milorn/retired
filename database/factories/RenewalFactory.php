<?php

namespace Database\Factories;

use App\Enums\RenewalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Renewal>
 */
class RenewalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(RenewalStatus::class),
            'year' => fake()->year(),
            'description' => fake()->optional()->paragraph(),
            'answer' => fake()->optional()->paragraph(),
        ];
    }
}
