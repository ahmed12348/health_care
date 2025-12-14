<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * 
     * All test users have password: "password"
     */
    public function run(): void
    {
        // Create or update admin user
        // Email: admin@example.com | Password: password
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'loyalty_points' => 0,
            ]
        );

        // Create or update test customer user with initial loyalty points for testing
        // Email: customer@example.com | Password: password | Loyalty Points: 500
        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Test Customer',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'user',
                'loyalty_points' => 500, // Starting with 500 points for testing
            ]
        );

        // Create or update additional test customers
        // Email: john@example.com | Password: password | Loyalty Points: 1000
        User::updateOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'user',
                'loyalty_points' => 1000,
            ]
        );

        // Email: jane@example.com | Password: password | Loyalty Points: 0
        User::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'user',
                'loyalty_points' => 0,
            ]
        );
    }
}
