<?php

namespace Database\Seeders;

use App\Models\ListContactRelationship;
use Illuminate\Database\Seeder;

class ListContactRelationshipSeeder extends Seeder
{
    public function run()
    {
        \App\Models\MailingList::factory(5)->create();
        \App\Models\EmailContact::factory(10)->create();

        ListContactRelationship::factory(20)->create();
    }
}
