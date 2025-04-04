<?php

namespace Tests\Feature;

use App\Models\MailingList;
use App\Models\User;
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
        // Crear un usuario válido para usarlo como owner
        $user = User::factory()->create();

        $mailingListData = [
            'name' => 'Newsletter Subscribers',
            'description' => 'Monthly newsletter',
            'creation_date' => now()->toDateString(),
            'last_updated_date' => now()->toDateString(),
            'owner_id' => $user->id, // Usamos el id del usuario creado
            'status' => 'active',
            'type' => 'promotions',
            'tags' => 'news,monthly'
        ];

        $response = $this->postJson('/api/mailinglists', $mailingListData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'status'
                ]
            ]);

        $this->assertDatabaseHas('mailing_lists', [
            'name' => 'Newsletter Subscribers'
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
                    'type' => $mailingList->type,
                ]
            ]);
    }

    #[Test]
    public function test_it_can_update_a_mailing_list(): void
    {
        // Crear un mailing list
        $mailingList = MailingList::factory()->create();

        // Datos para actualizar
        $updateData = [
            'name' => 'Updated Mailing List',
        ];

        // Enviar request PUT para actualizar
        $response = $this->putJson("/api/mailinglists/{$mailingList->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $mailingList->id,
                    'name' => 'Updated Mailing List',
                ],
            ]);

        $this->assertDatabaseHas('mailing_lists', [
            'id' => $mailingList->id,
            'name' => 'Updated Mailing List',
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