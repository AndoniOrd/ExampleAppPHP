<?php

namespace Tests\Feature;

use App\Models\EmailTemplates;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EmailTemplatesJsonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the index endpoint returns a JSON list of email templates.
     */
    #[Test]
    public function testIndexReturnsEmailTemplatesAsJson()
    {
        // Create 3 email templates using the factory
        EmailTemplates::factory()->count(3)->create();

        $response = $this->getJson('/api/email_templates');

        $response->assertOk()
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'name',
                         'description',
                         'subject_line',
                         'html_content',
                         'plain_text_version',
                         'creator',
                         'creation_date',
                         'last_updated_date',
                         'category',
                         'status',
                         'preview_image_url',
                         'created_at',
                         'updated_at'
                     ]
                 ]);
    }

    /**
     * Test that the store endpoint creates an email template and returns JSON.
     */
    #[Test]
    public function testStoreCreatesEmailTemplateAndReturnsJson()
    {
        // Create a related user for the creator field
        $user = User::factory()->create();

        $data = [
            'name'                => 'Test Template',
            'description'         => 'Test Description',
            'subject_line'        => 'Test Subject',
            'html_content'        => '<p>Test HTML Content</p>',
            'plain_text_version'  => 'Test plain text version',
            'creator'             => $user->id,
            'creation_date'       => Carbon::now()->format('Y-m-d'),
            'last_updated_date'   => Carbon::now()->format('Y-m-d'),
            'category'            => 'Test Category',
            // Ensure this value is one of the allowed ones ('draft', 'active', 'archived')
            'status'              => 'active',
            'preview_image_url'   => 'https://example.com/image.jpg'
        ];

        $response = $this->postJson('/api/email_templates', $data);

        $response->assertCreated()
                 ->assertJsonFragment([
                     'name'       => 'Test Template',
                     'subject_line' => 'Test Subject',
                     'category'   => 'Test Category',
                     'status'     => 'active',
                 ]);

        $this->assertDatabaseHas('email_templates', [
            'name' => 'Test Template'
        ]);
    }

    /**
     * Test that the show endpoint returns a specific email template as JSON.
     */
    #[Test]
    public function testShowReturnsEmailTemplateAsJson()
    {
        $template = EmailTemplates::factory()->create();

        $response = $this->getJson("/api/email_templates/{$template->id}");

        $response->assertOk()
                 ->assertJsonFragment([
                     'id'   => $template->id,
                     'name' => $template->name,
                 ]);
    }

    /**
     * Test that the update endpoint modifies an existing email template and returns JSON.
     */
    #[Test]
    public function testUpdateModifiesEmailTemplateAndReturnsJson()
    {
        $template = EmailTemplates::factory()->create([
            'name'   => 'Original Name',
            'status' => 'draft'
        ]);

        $updateData = [
            'name'   => 'Updated Name',
            'status' => 'active'
        ];

        $response = $this->putJson("/api/email_templates/{$template->id}", $updateData);

        $response->assertOk()
                 ->assertJsonFragment([
                     'name'   => 'Updated Name',
                     'status' => 'active'
                 ]);

        $this->assertDatabaseHas('email_templates', [
            'id'   => $template->id,
            'name' => 'Updated Name',
            'status' => 'active'
        ]);
    }

    /**
     * Test that the destroy endpoint deletes the specified email template.
     */
    #[Test]
    public function testDestroyDeletesEmailTemplate()
    {
        $template = EmailTemplates::factory()->create();

        $response = $this->deleteJson("/api/email_templates/{$template->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('email_templates', [
            'id' => $template->id
        ]);
    }
}
