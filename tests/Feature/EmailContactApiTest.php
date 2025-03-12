<?php

namespace Tests\Feature;

use App\Models\EmailContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailContactApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_it_can_list_all_email_contacts()
    {
        EmailContact::factory()->count(3)->create();

        $response = $this->getJson(route('email-contacts.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'email_address',
                    'first_name',
                    'last_name',
                    'status',
                    'source',
                    'opt_in_date',
                    'opt_in_confirmation',
                    'custom_fields',
                    'creation_date',
                    'last_updated_date',
                ]
            ])
            ->assertJsonCount(3);
    }

    #[Test]
    public function test_it_can_create_an_email_contact()
    {
        $contactData = [
            'email_address' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'status' => 'active',
            'source' => 'web',
            'opt_in_date' => Carbon::now()->toDateTimeString(),
            'opt_in_confirmation' => true,
            'custom_fields' => '{"company": "ACME"}',
            'creation_date' => Carbon::now()->toDateTimeString(),
            'last_updated_date' => Carbon::now()->toDateTimeString(),
        ];

        $response = $this->postJson(route('email-contacts.store'), $contactData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'email_address',
                'first_name',
                'last_name',
                'status'
            ]);

        $this->assertDatabaseHas('email_contacts', [
            'email_address' => 'test@example.com',
            'status' => 'active'
        ]);
    }

    #[Test]
    public function test_it_validates_required_fields_on_create()
    {
        $response = $this->postJson(route('email-contacts.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email_address',
                'first_name',
                'last_name',
                'status',
                'source',
                'opt_in_date',
                'opt_in_confirmation',
                'creation_date',
                'last_updated_date'
            ]);
    }

    #[Test]
    public function test_it_can_show_an_email_contact()
    {
        $contact = EmailContact::factory()->create();

        $response = $this->getJson(route('email-contacts.show', $contact->id));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $contact->id,
                'email_address' => $contact->email_address
            ]);
    }

    #[Test]
    public function test_it_can_update_an_email_contact()
    {
        $contact = EmailContact::factory()->create();
        $updateData = [
            'first_name' => 'Updated',
            'email_address' => 'updated@example.com',
            'status' => 'inactive'
        ];

        $response = $this->putJson(route('email-contacts.update', $contact->id), $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'first_name' => 'Updated',
                'email_address' => 'updated@example.com',
                'status' => 'inactive'
            ]);

        // Change this line to use 'email_contacts' instead of 'contacts'
        $this->assertDatabaseHas('email_contacts', [
            'id' => $contact->id,
            'first_name' => 'Updated'
        ]);
    }


    #[Test]
    public function test_it_validates_unique_email_on_update()
    {
        $contact1 = EmailContact::factory()->create();
        $contact2 = EmailContact::factory()->create();

        $response = $this->putJson(route('email-contacts.update', $contact1->id), [
            'email_address' => $contact2->email_address
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email_address');
    }

    #[Test]
    public function test_it_can_delete_an_email_contact()
    {
        $contact = EmailContact::factory()->create();

        $response = $this->deleteJson(route('email-contacts.destroy', $contact->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    #[Test]
    public function test_it_validates_enum_values()
    {
        $response = $this->postJson(route('email-contacts.store'), [
            'status' => 'invalid-status',
            'source' => 'invalid-source'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status', 'source']);
    }

    #[Test]
    public function test_it_validates_date_fields()
    {
        $response = $this->postJson(route('email-contacts.store'), [
            'opt_in_date' => 'not-a-date',
            'creation_date' => 'invalid-date',
            'last_updated_date' => 'invalid-date'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['opt_in_date', 'creation_date', 'last_updated_date']);
    }

    #[Test]
    public function test_it_validates_custom_fields_as_json()
    {
        $response = $this->postJson(route('email-contacts.store'), [
            'custom_fields' => 'invalid-json'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('custom_fields');
    }
}