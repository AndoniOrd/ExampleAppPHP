<?php

namespace Tests\Feature;

use App\Enums\TrackingOptions;
use App\Models\CampaignPlanning;
use App\Models\EmailTemplates;
use App\Models\MailingList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CampaignPlanningApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_it_can_list_all_campaign_plannings(): void
    {
        CampaignPlanning::factory()->count(3)->create();

        $response = $this->getJson(route('campaign-plannings.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'email_template_id',
                        'mailing_list_id',
                        'scheduled_time',
                        'time_zone',
                        'status_status_type',
                        'scheduled_by',
                        'send_from_email',
                        'send_from_name',
                        'reply_to_email',
                        'tracking_options',
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function test_it_can_create_a_campaign_planning(): void
    {
        $user = User::factory()->create();
        $emailTemplate = EmailTemplates::factory()->create();
        $mailingList = MailingList::factory()->create();

        $campaignData = [
            'name' => 'Summer Campaign',
            'description' => 'Summer promotion campaign',
            'email_template_id' => $emailTemplate->id,
            'mailing_list_id' => $mailingList->id,
            'scheduled_time' => Carbon::now()->addWeek()->toDateTimeString(),
            'time_zone' => 'America/New_York',
            'status_status_type' => 'draft',
            'scheduled_by' => $user->id,
            'send_from_email' => 'noreply@example.com',
            'send_from_name' => 'Marketing Team',
            'reply_to_email' => 'contact@example.com',
            'tracking_options' => TrackingOptions::CLICKS->value,
        ];

        $response = $this->postJson('/campaign-plannings', $campaignData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'status',
                    'scheduled_time'
                ]
            ]);

        $this->assertDatabaseHas('campaign_plannings', [
            'name' => 'Summer Campaign',
            'status_status_type' => 'draft'
        ]);
    }

    #[Test]
    public function test_it_validates_required_fields(): void
    {
        $response = $this->postJson('/campaign-plannings', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email_template_id',
                'mailing_list_id',
                'scheduled_time',
                'time_zone',
                'status_status_type',
                'scheduled_by',
                'send_from_email',
                'send_from_name',
                'reply_to_email',
                'tracking_options'
            ]);
    }

    #[Test]
    public function test_it_can_show_a_campaign_planning(): void
    {
        $campaignPlanning = CampaignPlanning::factory()->create();

        $response = $this->getJson("/campaign-plannings/{$campaignPlanning->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $campaignPlanning->id,
                    'name' => $campaignPlanning->name,
                    'status' => $campaignPlanning->status_status_type,
                    'scheduled_time' => $campaignPlanning->scheduled_time->toDateTimeString(),
                ]
            ]);
    }

    #[Test]
    public function test_it_can_update_a_campaign_planning(): void
    {
        $campaignPlanning = CampaignPlanning::factory()->create();
        $newTemplate = EmailTemplates::factory()->create();

        $updateData = [
            'name' => 'Updated Campaign Name',
            'email_template_id' => $newTemplate->id,
            'status_status_type' => 'scheduled'
        ];

        $response = $this->putJson("/campaign-plannings/{$campaignPlanning->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $campaignPlanning->id,
                    'name' => 'Updated Campaign Name',
                    'status' => 'scheduled'
                ]
            ]);

        $this->assertDatabaseHas('campaign_plannings', [
            'id' => $campaignPlanning->id,
            'email_template_id' => $newTemplate->id,
            'status_status_type' => 'scheduled'
        ]);
    }

    #[Test]
    public function test_it_can_delete_a_campaign_planning(): void
    {
        $campaignPlanning = CampaignPlanning::factory()->create();

        $response = $this->deleteJson("/campaign-plannings/{$campaignPlanning->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('campaign_plannings', ['id' => $campaignPlanning->id]);
    }

    #[Test]
    public function test_it_validates_tracking_options_enum(): void
    {
        $invalidData = CampaignPlanning::factory()->make()->toArray();
        $invalidData['tracking_options'] = 'invalid_tracking_option';

        $response = $this->postJson('/campaign-plannings', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('tracking_options');
    }

    #[Test]
    public function test_it_validates_timezone_format(): void
    {
        $invalidData = CampaignPlanning::factory()->make()->toArray();
        $invalidData['time_zone'] = 'Invalid/Timezone';

        $response = $this->postJson('/campaign-plannings', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('time_zone');
    }
}