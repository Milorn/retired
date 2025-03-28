<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'identifier' => 'admin',
        ], [
            'password' => 'admin@2025',
            'type' => UserType::Admin,
        ]);
    }
}
