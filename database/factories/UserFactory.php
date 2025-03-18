<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
        'last_name' => fake()->lastName(),
        'email_address' => fake()->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
        'phone_number' => $this->faker->phoneNumber,
        'account_status' => 'active',
        'creation_date' => now(),
            'last_login'       => null,
            'company_name'     => $this->faker->company,
            'company_address'  => $this->faker->address,
            'vat_tax_id'       => $this->faker->ean8,
            'industry'         => $this->faker->word,
            'company_size'     => $this->faker->numberBetween(1, 1000),
            'website'          => $this->faker->url,
            'remember_token'   => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
