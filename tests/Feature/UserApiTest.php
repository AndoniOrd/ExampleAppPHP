<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function it_can_list_all_users(): void
    {
        // Create multiple users in the database
        User::factory()->count(3)->create();
    
        // Send a GET request to the named route 'users.index'
        $response = $this->getJson(route('users.index'));
    
        // Assert that the response status is 200 and the response structure is correct
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email_address',
                        'role',
                        'account_status'
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data'); // Ensure that 3 users are returned
    }
   
    #[Test]
    public function it_can_create_a_user(): void
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'john.doe@example.com',
            'password' => 'SecurePassword123!',
            'phone_number' => '+1234567890',
            'role' => 'user',
            'account_status' => 'active',
            'company_name' => 'Test Corp',
            'vat_tax_id' => 'GB123456789'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'first_name',
                    'email_address',
                    'role'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email_address' => 'john.doe@example.com',
            'company_name' => 'Test Corp'
        ]);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email_address',
                'password'
            ]);
    }

    #[Test]
    public function it_validates_email_format(): void
    {
        $response = $this->postJson('/api/users', [
            'email_address' => 'invalid-email'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email_address');
    }

    #[Test]
    public function it_can_show_a_user(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'email_address' => $user->email_address
                ]
            ]);
    }

    #[Test]
    public function it_can_update_a_user(): void
    {
        $user = User::factory()->create();

        $updateData = [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'company_size' => 'medium'
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Updated',
                    'company_size' => 'medium'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'last_name' => 'Name'
        ]);
    }

    #[Test]
    public function it_can_delete_a_user(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    #[Test]
    public function it_protects_sensitive_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertJsonMissing([
            'password',
            'remember_token'
        ]);
    }

    #[Test]
    public function it_enforces_unique_email(): void
    {
        $role = Role::factory()->create();
       $user = User::factory()->create(['email_address' => 'duplicate@example.com']);
       $user->addRole($role);

        $response = $this->actingAs($user)->getJson('/api/users', [
            'email_address' => 'duplicate@example.com'
        ]);

        dd($response->json());

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email_address');
    }
}