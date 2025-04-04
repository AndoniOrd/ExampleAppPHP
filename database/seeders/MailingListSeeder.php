<?php

namespace Database\Seeders;

use App\Models\MailingList;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MailingListSeeder extends Seeder
{
    public function run()
    {
        $mailingLists = [
            [
                'name' => 'voluptatem molestias Newsletter',
                'description' => 'Corrupti error ipsa dolores consequatur architecto...',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 11,
                'status' => 'draft',
                'type' => 'promotions',
                'tags' => 'molestias',
            ],
            [
                'name' => 'sit accusamus Newsletter',
                'description' => 'Occaecati ipsum possimus ratione quia voluptatem e...',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 12,
                'status' => 'draft',
                'type' => 'promotions',
                'tags' => 'sint',
            ],
            [
                'name' => 'id ea Newsletter',
                'description' => 'Quisquam nulla facere voluptatem ad occaecati exce...',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 13,
                'status' => 'draft',
                'type' => 'updates',
                'tags' => 'impedit',
            ],
            [
                'name' => 'perspiciatis dolorem Newsletter',
                'description' => 'Consequatur libero fuga eos et non qui.',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 14,
                'status' => 'active',
                'type' => 'updates',
                'tags' => 'aliquam',
            ],
            [
                'name' => 'rerum consequuntur Newsletter',
                'description' => 'Minus omnis consequatur eligendi a praesentium.',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 15,
                'status' => 'draft',
                'type' => 'updates',
                'tags' => 'nam',
            ],
            [
                'name' => 'ipsum provident Newsletter',
                'description' => 'Recusandae libero dolores aliquid delectus occaeca...',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 16,
                'status' => 'active',
                'type' => 'updates',
                'tags' => 'sapiente',
            ],
            [
                'name' => 'in fugit Newsletter',
                'description' => 'Qui ipsa perferendis a repellat voluptate laborum ...',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 17,
                'status' => 'active',
                'type' => 'newsletter',
                'tags' => 'nihil',
            ],
            [
                'name' => 'ut doloremque Newsletter',
                'description' => 'Vel delectus dolore enim aut est aperiam.',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 18,
                'status' => 'active',
                'type' => 'newsletter',
                'tags' => 'et',
            ],
            [
                'name' => 'est et Newsletter',
                'description' => 'Ipsam sed distinctio architecto.',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 19,
                'status' => 'archived',
                'type' => 'newsletter',
                'tags' => 'sit',
            ],
            [
                'name' => 'commodi quibusdam Newsletter',
                'description' => 'Veritatis itaque magnam possimus numquam est rerum...',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 20,
                'status' => 'active',
                'type' => 'promotions',
                'tags' => 'quas',
            ],
            [
                'name' => 'voluptas qui Newsletter',
                'description' => 'Rem qui modi dolores a.',
                'creation_date' => '2025-03-24',
                'last_updated_date' => '2025-03-24',
                'owner_id' => 22,
                'status' => 'active',
                'type' => 'updates',
                'tags' => 'aut',
            ]
        ];

        foreach ($mailingLists as $listData) {
            MailingList::create([
                'name' => $listData['name'],
                'description' => $listData['description'],
                'creation_date' => Carbon::parse($listData['creation_date']),
                'last_updated_date' => Carbon::parse($listData['last_updated_date']),
                'owner_id' => $listData['owner_id'],
                'status' => $listData['status'],
                'type' => $listData['type'],
                'tags' => $listData['tags'],
                'created_at' => Carbon::parse($listData['creation_date']),
                'updated_at' => Carbon::parse($listData['last_updated_date']),
            ]);
        }
    }
}