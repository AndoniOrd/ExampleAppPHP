<?php

namespace Database\Factories;

use App\Models\ListContactRelationship;
use App\Models\MailingList;
use App\Models\EmailContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListContactRelationshipFactory extends Factory
{
    protected $model = ListContactRelationship::class;

    public function definition()
    {
        return [
            'list_id' => MailingList::factory(),
            'contact_id' => EmailContact::factory(),
            'subscription_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['subscribed', 'unsubscribed', 'pending']),
            
        ];
    }
}
