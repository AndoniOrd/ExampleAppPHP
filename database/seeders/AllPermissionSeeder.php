<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AllPermissionSeeder extends Seeder
{
    /**
     * Run the database seeders for all permissions.
     */
    public function run()
    {
        $this->call([
            UserPermissionSeeder::class,
            // Add additional permission seeders as needed
        ]);
    }
}
