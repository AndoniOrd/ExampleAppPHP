<?php

namespace Tests\Feature;

use App\Models\CampaignReport;
use App\Models\CampaignPlanning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CampaignReportApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_it_displays_the_campaign_reports_index_page()
    {
        CampaignReport::factory()->create();
    
        // Act: Make a GET request to the index route expecting JSON
        $response = $this->getJson(route('campaign-reports.index'));
    
        // Assert: Verify the response structure
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'total_recipients',
                    'successful_deliveries',
                    // Add other fields as necessary
                ],
            ],
        ]);
    }
    
    #[Test]
    public function it_displays_the_create_campaign_report_page()
    {
        // Act: Make a GET request to the create route expecting JSON
        $response = $this->getJson(route('campaign-reports.create'));

        // Assert: Verify the response
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'campaignPlannings',
        ]);
    }

    #[Test]
    public function it_stores_a_new_campaign_report()
    {
        // Arrange: Create a campaign planning instance
        $campaignPlanning = CampaignPlanning::factory()->create();

        // Prepare the data to be sent in the request
        $data = [
            'campaign_planning_id'   => $campaignPlanning->id,
            'total_recipients'       => 1000,
            'successful_deliveries'  => 900,
            'hard_bounces'           => 50,
            'soft_bounces'           => 50,
            'opens_count'            => 800,
            'opens_unique'           => 700,
            'clicks_count'           => 300,
            'clicks_unique'          => 250,
            'unsubscribes'           => 20,
            'spam_complaints'        => 5,
            'device_statistics'      => [
                'desktop' => 400,
                'mobile'  => 500,
                'tablet'  => 100,
                'unknown' => 0,
            ],
            'geographical_data'      => [
                'USA'    => 500,
                'Canada' => 300,
                'UK'     => 200,
            ],
            'time_based_metrics'     => [
                'morning'   => 400,
                'afternoon' => 300,
                'evening'   => 200,
            ],
            'engagement_score'       => 85.5,
        ];

        // Act: Make a POST request to the store route as JSON
        $response = $this->postJson(route('campaign-reports.store'), $data);

        // Assert: Verify the response and database state
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Campaign report created successfully.',
        ]);
        $this->assertDatabaseHas('campaign_reports', [
            'campaign_planning_id' => $campaignPlanning->id,
            'total_recipients'     => 1000,
            // Add additional fields as needed
        ]);
    }

    #[Test]
    public function it_displays_a_specific_campaign_report()
    {
        // Arrange: Create a campaign report
        $campaignPlanning = CampaignPlanning::factory()->create();
        $campaignReport = CampaignReport::factory()->create([
            'campaign_planning_id' => $campaignPlanning->id,
        ]);

        // Act: Make a GET request to the show route expecting JSON
        $response = $this->getJson(route('campaign-reports.show', $campaignReport->id));

        // Assert: Verify the response structure and content
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $campaignReport->id,
        ]);
    }

    #[Test]
    public function it_displays_the_edit_campaign_report_page()
    {
        // Arrange: Create a campaign report
        $campaignPlanning = CampaignPlanning::factory()->create();
        $campaignReport = CampaignReport::factory()->create([
            'campaign_planning_id' => $campaignPlanning->id,
        ]);

        // Act: Make a GET request to the edit route expecting JSON
        $response = $this->getJson(route('campaign-reports.edit', $campaignReport->id));

        // Assert: Verify the JSON response contains both the report and available campaign plannings
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'campaignReport',
            'campaignPlannings',
        ]);
        $response->assertJsonFragment([
            'id' => $campaignReport->id,
        ]);
    }

    #[Test]
    public function test_it_updates_a_campaign_report()
    {
        // Arrange: Create a campaign report
        $campaignReport = CampaignReport::factory()->create();
    
        // Define the updated data with all required fields
        $updatedData = [
            'campaign_planning_id'   => $campaignReport->campaign_planning_id,
            'total_recipients'       => 1200,
            'successful_deliveries'  => 1100, // Must be ≤ total_recipients
            'hard_bounces'           => 10,
            'soft_bounces'           => 5,
            'opens_count'            => 150,
            'opens_unique'           => 120,
            'clicks_count'           => 70,
            'clicks_unique'          => 60,
            'unsubscribes'           => 3,
            'spam_complaints'        => 1,
            'device_statistics'      => [
                'desktop' => 60,
                'mobile'  => 40,
                'tablet'  => 10,
                'unknown' => 0,
            ],
            'geographical_data'      => [
                'US' => 50,
                'UK' => 30,
            ],
            'time_based_metrics'     => [
                'morning'   => 30,
                'afternoon' => 70,
                'evening'   => 20,
            ],
            'engagement_score'       => 85,
        ];
    
        // Act: Make a PUT request to the update route as JSON
        $response = $this->putJson(route('campaign-reports.update', $campaignReport->id), $updatedData);
    
        // Assert: Verify the response and database state
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Campaign report updated successfully.',
        ]);
        $this->assertDatabaseHas('campaign_reports', [
            'id'               => $campaignReport->id,
            'total_recipients' => 1200,
            // Add additional assertions as necessary
        ]);
    }
    
    #[Test]
    public function it_deletes_a_campaign_report()
    {
        // Arrange: Create a campaign report
        $campaignPlanning = CampaignPlanning::factory()->create();
        $campaignReport = CampaignReport::factory()->create([
            'campaign_planning_id' => $campaignPlanning->id,
        ]);

        // Act: Make a DELETE request to the destroy route expecting JSON
        $response = $this->deleteJson(route('campaign-reports.destroy', $campaignReport->id));

        // Assert: Verify the response and database state
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Campaign report deleted successfully.',
        ]);
        $this->assertDatabaseMissing('campaign_reports', [
            'id' => $campaignReport->id,
        ]);
    }
}
