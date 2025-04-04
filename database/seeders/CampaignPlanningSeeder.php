<?php

namespace Database\Seeders;

use App\Models\CampaignPlanning;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CampaignPlanningSeeder extends Seeder
{
    public function run()
    {
        // Create 10 campaign planning entries using the factory
        CampaignPlanning::factory()->count(10)->create();
    }
}