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
    }
}
