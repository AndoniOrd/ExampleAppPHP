<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class CampaignReportsSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to avoid constraint violations
        Schema::disableForeignKeyConstraints();

        // Initialize Faker for generating fake data
        $faker = Faker::create();

        // Retrieve all campaign IDs to assign to campaign_reports
        $campaignIds = DB::table('campaigns')->pluck('id');

        // Generate and insert 50 campaign reports
        foreach (range(1, 50) as $index) {
            DB::table('campaign_reports')->insert([
                'campaign_id' => $faker->randomElement($campaignIds),
                'total_recipients' => $faker->numberBetween(1000, 5000),
                'successful_deliveries' => $faker->numberBetween(800, 4500),
                'hard_bounces' => $faker->numberBetween(0, 100),
                'soft_bounces' => $faker->numberBetween(0, 100),
                'opens_count' => $faker->numberBetween(500, 4000),
                'opens_unique' => $faker->numberBetween(200, 3000),
                'clicks_count' => $faker->numberBetween(100, 2000),
                'clicks_unique' => $faker->numberBetween(50, 1500),
                'click_to_open_rate' => $faker->randomFloat(2, 0, 1),
                'unsubscribes' => $faker->numberBetween(0, 50),
                'spam_complaints' => $faker->numberBetween(0, 20),
                'device_statistics' => json_encode([
                    'desktop' => $faker->numberBetween(0, 1000),
                    'mobile' => $faker->numberBetween(0, 1000),
                    'tablet' => $faker->numberBetween(0, 1000),
                ]),
                'geographical_data' => json_encode([
                    'US' => $faker->numberBetween(0, 1000),
                    'EU' => $faker->numberBetween(0, 1000),
                    'AS' => $faker->numberBetween(0, 1000),
                ]),
                'time_based_metrics' => json_encode([
                    'morning' => $faker->numberBetween(0, 1000),
                    'afternoon' => $faker->numberBetween(0, 1000),
                    'evening' => $faker->numberBetween(0, 1000),
                ]),
                'engagement_score' => $faker->randomFloat(2, 0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Enable foreign key checks back
        Schema::enableForeignKeyConstraints();
    }
}
