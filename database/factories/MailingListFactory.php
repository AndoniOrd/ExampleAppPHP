<?php
namespace Database\Factories;

use App\Models\MailingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailingListFactory extends Factory
{
    protected $model = MailingList::class;

    public function definition(): array
    {
        // Create a new User using the User factory
        $user = User::factory()->create();

        return [
            'name' => $this->faker->words(2, true) . ' Newsletter',
            'description' => $this->faker->sentence(),
            'creation_date' => now()->toDateString(),
            'last_updated_date' => now()->toDateString(),
            'owner_id' => $user->id,
            'status' => $this->faker->randomElement(['draft', 'active', 'archived']),
            'type' => $this->faker->randomElement(['newsletter', 'promotions', 'updates']),
            'tags' => $this->faker->word()
        ];
    }
}