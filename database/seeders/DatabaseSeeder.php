<?php

namespace Database\Seeders;

use App\Models\MailingList;
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
        MailingList::factory(10)->create();
        
        // Call the LaratrustSeeder properly
        $this->call([
            LaratrustSeeder::class
        ]);

        $this->call([
            EmailTemplatesSeeder::class,
        ]);
    }
}