<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Retiree>
 */
class RetireeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->type(UserType::Retiree),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'number' => fake()->ean13(),
            'birthdate' => fake()->optional()->date(),
            'email' => fake()->optional()->email(),
            'phone' => fake()->optional()->e164PhoneNumber(),
        ];
    }
}
