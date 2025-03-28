<?php

namespace Database\Factories;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Claim>
 */
class ClaimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(ClaimStatus::class),
            'description' => fake()->paragraph(),
            'date' => fake()->date(),
        ];
    }
}
