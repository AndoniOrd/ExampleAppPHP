<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dictionary;
use App\Models\DictionaryItem;

class StatusDictionarySeeder extends Seeder
{
    public function run(): void
    {
        // Create or find the STATUS dictionary
        $statusDictionary = Dictionary::firstOrCreate(
            ['name' => 'STATUS'], // lookup criteria
            ['description' => 'Mailing list contact statuses'] // defaults to apply if not found
        );
        

        // Define status items
        $statuses = [
            ['name' => 'Subscribed', 'value' => 'subscribed', 'description' => 'Active subscriber'],
            ['name' => 'Unsubscribed', 'value' => 'unsubscribed', 'description' => 'Opted out'],
            ['name' => 'Pending', 'value' => 'pending', 'description' => 'Awaiting confirmation'],
        ];

        // Create or update dictionary items
        foreach ($statuses as $status) {
            DictionaryItem::firstOrCreate([
                'dictionary_id' => $statusDictionary->id,
                'value' => $status['value']
            ], [
                'name' => $status['name'],
                'description' => $status['description']
            ]);
        }
    }
}