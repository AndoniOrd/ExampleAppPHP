<?php

namespace Database\Factories;

use App\Models\MailingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class MailingListFactory extends Factory
{
    protected $model = MailingList::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word . ' Newsletter',
            'description' => $this->faker->sentence,
            'creation_date' => $this->faker->date(),
            'last_updated_date' => $this->faker->date(),
            'owner_id' => User::factory(), // Crea un usuario si no existe
            'status' => $this->faker->randomElement(['active', 'draft', 'archived']),
            'type' => $this->faker->randomElement(['newsletter', 'promotions', 'updates']),
            'tags' => $this->faker->words(3, true), // Comma-separated tags
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}