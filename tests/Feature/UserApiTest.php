<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

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

    #[Test]
    public function test_admin_can_create_user_with_role(): void
{
    // Create an admin user and assign the 'admin' role
    $admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $admin->assignRole($adminRole);

    // Create a 'user' role
    $userRole = Role::create(['name' => 'user']);

    // Data for the new user
    $userData = [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email_address' => 'jane.doe@example.com',
        'password' => 'SecurePassword123!',
        'role' => 'user',
    ];

    // Acting as the admin, send a POST request to create a new user
    $response = $this->actingAs($admin)->postJson(route('users.create'), $userData);

    // Assert the user was created successfully
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'first_name',
                'email_address',
                'role',
            ],
        ]);

    // Verify the user exists in the database with the assigned role
    $this->assertDatabaseHas('users', [
        'email_address' => 'jane.doe@example.com',
    ]);

    $createdUser = User::where('email_address', 'jane.doe@example.com')->first();
    $this->assertTrue($createdUser->hasRole('user'));
}

#[Test]
public function test_admin_can_update_user_role(): void
{
    // Create an admin user and assign the 'admin' role
    $admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $admin->assignRole($adminRole);

    // Create a user and assign the 'user' role
    $user = User::factory()->create();
    $userRole = Role::create(['name' => 'user']);
    $user->assignRole($userRole);

    // Create a 'manager' role
    $managerRole = Role::create(['name' => 'manager']);

    // Data to update the user's role
    $updateData = [
        'role' => 'manager',
    ];

    // Acting as the admin, send a PUT request to update the user's role
    $response = $this->actingAs($admin)->putJson(route('users.update', $user->id), $updateData);

    // Assert the user's role was updated successfully
    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'role' => 'manager',
            ],
        ]);

    // Verify the user's role was updated in the database
    $this->assertTrue($user->fresh()->hasRole('manager'));
}
#[Test]
public function test_admin_can_delete_user(): void
{
    // Create an admin user and assign the 'admin' role
    $admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $admin->assignRole($adminRole);

    // Create a user to be deleted
    $user = User::factory()->create();

    // Acting as the admin, send a DELETE request to remove the user
    $response = $this->actingAs($admin)->deleteJson(route('users.delete', $user->id));

    // Assert the user was deleted successfully
    $response->assertStatus(204);

    // Verify the user is soft deleted in the database
    $this->assertSoftDeleted('users', ['id' => $user->id]);
}

#[Test]
public function test_non_admin_cannot_create_user(): void
{
    // Create a non-admin user
    $user = User::factory()->create();

    // Data for the new user
    $userData = [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email_address' => 'jane.doe@example.com',
        'password' => 'SecurePassword123!',
        'role' => 'user',
    ];

    // Acting as the non-admin, attempt to create a new user
    $response = $this->actingAs($user)->postJson(route('users.create'), $userData);

    // Assert the action is forbidden
    $response->assertStatus(403);
}

}