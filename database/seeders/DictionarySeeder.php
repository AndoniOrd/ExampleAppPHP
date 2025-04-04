<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dictionary;

class DictionarySeeder extends Seeder
{
    public function run()
    {
        // Create or find the STATUS dictionary
        $statusDict = Dictionary::firstOrCreate([
            'name' => 'STATUS'
        ], [
            'description' => 'Subscription status codes'
        ]);
    
        // Define status items with consistent values and colors
        $statuses = [
            [
                'name' => 'Subscribed',
                'value' => 'subscribed',
                'description' => 'Active subscription',
                'order' => 1,
                'color' => 'success' // Verde
            ],
            [
                'name' => 'Unsubscribed',
                'value' => 'unsubscribed',
                'description' => 'Opted out',
                'order' => 2,
                'color' => 'danger' // Rojo
            ],
            [
                'name' => 'Pending',
                'value' => 'pending',
                'description' => 'Awaiting confirmation',
                'order' => 3,
                'color' => 'warning' // Naranja
            ]
        ];

        // Create or update dictionary items
        foreach ($statuses as $status) {
            $statusDict->items()->firstOrCreate(
                [
                    'value' => $status['value']
                ],
                [
                    'name' => $status['name'],
                    'description' => $status['description'],
                    'order' => $status['order']
                ]
            );
        }
    }
}