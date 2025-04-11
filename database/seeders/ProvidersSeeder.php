<?php
// database/seeders/ProvidersSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProvidersSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Provider::factory()
            ->count(5) // Create 5 test providers
            ->create();
    }
}