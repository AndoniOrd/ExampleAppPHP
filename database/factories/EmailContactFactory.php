<?php

namespace Database\Factories;

use App\Models\EmailContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailContactFactory extends Factory
{
    protected $model = EmailContact::class;

    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
            'source' => $this->faker->randomElement(['web', 'api', 'manual']),
            'opt_in_date' => $this->faker->date(),
            'opt_in_confirmation' => $this->faker->boolean(),
            'custom_fields' => ['key' => $this->faker->word()], // Direct array instead of json_encode
            'creation_date' => $this->faker->dateTimeBetween('-1 year'),
            'last_updated_date' => $this->faker->dateTimeBetween('-1 month'),
        ];
    }
}