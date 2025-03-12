<?php

namespace Database\Seeders;

use App\Models\CampaignPlanning;
use App\Models\CampaignReport;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CampaignReportsSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        // Create campaign plannings if none exist
        if (CampaignPlanning::count() === 0) {
            CampaignPlanning::factory()->count(5)->create();
        }

        // Create 20 campaign reports
        for ($i = 0; $i < 20; $i++) {
            $totalRecipients = $faker->numberBetween(1000, 10000);
            $successfulDeliveries = $faker->numberBetween(
                intval($totalRecipients * 0.8), 
                $totalRecipients
            );
            
            $possibleBounces = $totalRecipients - $successfulDeliveries;
            $hardBounces = $faker->numberBetween(0, $possibleBounces);
            $softBounces = $possibleBounces - $hardBounces;

            $opensCount = $faker->numberBetween(
                intval($successfulDeliveries * 0.2), 
                $successfulDeliveries
            );
            $opensUnique = $faker->numberBetween(
                intval($opensCount * 0.7), 
                $opensCount
            );
            $clicksCount = $faker->numberBetween(
                intval($opensCount * 0.1), 
                intval($opensCount * 0.5)
            );
            $clicksUnique = $faker->numberBetween(
                intval($clicksCount * 0.7), 
                $clicksCount
            );
            
            $clickToOpenRate = $opensUnique > 0 
                ? round(($clicksUnique / $opensUnique) * 100, 2)
                : 0;

            CampaignReport::create([
                'campaign_planning_id' => CampaignPlanning::inRandomOrder()->first()->id,
                'total_recipients' => $totalRecipients,
                'successful_deliveries' => $successfulDeliveries,
                'hard_bounces' => $hardBounces,
                'soft_bounces' => $softBounces,
                'opens_count' => $opensCount,
                'opens_unique' => $opensUnique,
                'clicks_count' => $clicksCount,
                'clicks_unique' => $clicksUnique,
                'click_to_open_rate' => $clickToOpenRate,
                'unsubscribes' => $faker->numberBetween(0, 50),
                'spam_complaints' => $faker->numberBetween(0, 20),
                'device_statistics' => json_encode([
                    'desktop' => $faker->numberBetween(20, 60),
                    'mobile'  => $faker->numberBetween(30, 70),
                    'tablet'  => $faker->numberBetween(0, 10),
                    'unknown' => $faker->numberBetween(0, 5)
                ]),
                'geographical_data' => json_encode([
                    $faker->countryCode() => $faker->numberBetween(100, 500),
                    $faker->countryCode() => $faker->numberBetween(50, 300),
                    $faker->countryCode() => $faker->numberBetween(50, 200)
                ]),
                'time_based_metrics' => json_encode([
                    'morning'   => $faker->numberBetween(100, 500),
                    'afternoon' => $faker->numberBetween(200, 600),
                    'evening'   => $faker->numberBetween(150, 550)
                ]),
                'engagement_score' => $faker->randomFloat(2, 30, 95)
            ]);
        }
    }
}
