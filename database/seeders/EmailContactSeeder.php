<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmailContactSeeder extends Seeder
{
    public function run()
    {
        \App\Models\EmailContact::factory(50)->create();
    }
}