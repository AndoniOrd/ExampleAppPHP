<?php

namespace Tests\Feature;

use App\Models\MailingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MailingListApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_it_can_list_all_mailing_lists(): void
    {
        MailingList::factory()->count(3)->create();

        $response = $this->getJson(route('mailinglists.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'creation_date',
                        'last_updated_date',
                        'owner_id',
                        'status',
                        'type',
                        'tags'
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function test_it_can_create_a_mailing_list(): void
    {
        $mailingListData = [
            'name' => 'Newsletter Subscribers',
            'description' => 'Monthly newsletter',
            'creation_date' => now()->toDateString(),
            'last_updated_date' => now()->toDateString(),
            'owner_id' => 1, // Ensure this user exists in your test database
            'status' => 'active',
            'type' => 'newsletter',
            'tags' => 'news,monthly'
        ];

        $response = $this->postJson('/api/mailinglists', $mailingListData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'status',
                    'type'
                ]
            ]);

        $this->assertDatabaseHas('mailing_lists', [
            'name' => 'Newsletter Subscribers',
            'type' => 'newsletter'
        ]);
    }

    #[Test]
    public function test_it_validates_required_fields(): void
    {
        $response = $this->postJson('/api/mailinglists', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'creation_date',
                'owner_id',
                'status',
                'type'
            ]);
    }

    #[Test]
    public function test_it_can_show_a_mailing_list(): void
    {
        $mailingList = MailingList::factory()->create();

        $response = $this->getJson("/api/mailinglists/{$mailingList->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $mailingList->id,
                    'name' => $mailingList->name,
                    'type' => $mailingList->type
                ]
            ]);
    }

    #[Test]
    public function test_it_can_update_a_mailing_list(): void
    {
        // Create a mailing list
        $mailingList = MailingList::factory()->create();

        // Data to update
        $updateData = [
            'name' => 'Updated Mailing List',
            'type' => 'promotions' // Ensure this is a valid type from the migration
        ];

        // Send PUT request to update the mailing list
        $response = $this->putJson("/api/mailinglists/{$mailingList->id}", $updateData);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id'   => $mailingList->id,
                    'name' => 'Updated Mailing List',
                    'type' => 'promotions'
                ],
            ]);

        // Ensure the mailing list was updated in the database
        $this->assertDatabaseHas('mailing_lists', [
            'id'   => $mailingList->id,
            'name' => 'Updated Mailing List',
            'type' => 'promotions'
        ]);
    }

    #[Test]
    public function test_it_can_delete_a_mailing_list(): void
    {
        $mailingList = MailingList::factory()->create();

        $response = $this->deleteJson("/api/mailinglists/{$mailingList->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('mailing_lists', ['id' => $mailingList->id]);
    }
}