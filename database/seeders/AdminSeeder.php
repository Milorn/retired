<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
            'email' => 'admin@retired.dz',
        ], [
            'name' => 'Admin',
            'password' => 'retired@2025',
            'role' => UserRole::Admin,
        ]);
    }
}
