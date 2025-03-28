<?php

namespace Database\Factories;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identifier' => fake()->unique()->word(),
            'password' => static::$password ??= Hash::make('password'),
            'type' => fake()->randomElement([UserType::Agent, UserType::Retired]),
        ];
    }

    public function type(UserType $type): Factory
    {
        return $this->state(function () use ($type) {
            return [
                'type' => $type,
            ];
        });
    }
}
