<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed test users - only for development environments.
     * WARNING: Never run this in production!
     */
    public function run(): void
    {
        // Only seed test users in development/testing environments
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.de',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@test.de',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_active' => true,
        ]);
    }
}
