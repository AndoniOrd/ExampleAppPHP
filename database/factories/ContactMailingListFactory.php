<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMailingListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contact_id' => \App\Models\Contact::factory(),
            'mailing_list_id' => \App\Models\MailingList::factory(),
        ];
    }
}