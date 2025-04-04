<?php

namespace Database\Factories;

use App\Enums\SourceEnum;
use App\Enums\ContactStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $creationDate = fake()->dateTimeBetween('-1 year', 'now');
        $creationDateStr = $creationDate->format('Y-m-d');

        $optInDate = fake()->dateTimeBetween($creationDate, 'now');
        $optInDateStr = $optInDate->format('Y-m-d');

        $lastUpdatedDate = fake()->dateTimeBetween($optInDate, 'now')->format('Y-m-d');

        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'status' => fake()->randomElement(ContactStatusEnum::values()),
            'source' => fake()->randomElement(SourceEnum::values()),
            'opt_in_date' => $optInDateStr,
            'opt_in_confirmation' => fake()->boolean(),
            'custom_fields' => [
                'note' => fake()->sentence(),
            ],
            'creation_date' => $creationDateStr,
            'last_updated_date' => $lastUpdatedDate,
        ];
    }
}