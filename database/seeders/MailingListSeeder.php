<?php

namespace Database\Seeders;

use App\Models\MailingList;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MailingListSeeder extends Seeder
{
    public function run()
    {
        MailingList::create([
            'name' => 'Monthly Newsletter',
            'description' => 'A monthly update on the latest news and trends.',
            'creation_date' => Carbon::now()->subMonths(2), // Fecha de hace 2 meses
            'last_updated_date' => Carbon::now()->subMonths(1), // Fecha de hace 1 mes
            'owner_id' => 1, // ID del primer usuario
            'status' => 'active',
            'type' => 'newsletter',
            'tags' => 'news, updates, monthly',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        MailingList::create([
            'name' => 'Promotions',
            'description' => 'Latest deals and promotions from our store.',
            'creation_date' => Carbon::now()->subMonths(3),
            'last_updated_date' => Carbon::now()->subMonths(1),
            'owner_id' => 2, // ID del segundo usuario
            'status' => 'draft',
            'type' => 'promotions',
            'tags' => 'discounts, sales, promotions',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        MailingList::create([
            'name' => 'Updates',
            'description' => 'Updates and new features in our platform.',
            'creation_date' => Carbon::now()->subMonths(1),
            'last_updated_date' => Carbon::now()->subWeek(),
            'owner_id' => 3, // ID del tercer usuario
            'status' => 'active',
            'type' => 'updates',
            'tags' => 'new features, updates, product changes',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
