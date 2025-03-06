<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 random users
        User::factory(10)->create();

        // Create specific test user
        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email_address' => 'test@example.com',
            'phone_number' => '1-234-567-8901', // Add required field
            'role' => 'viewer', // Add required field
            'account_status' => 'active', // Add required field
            'creation_date' => now(),
        ]);
    }
}