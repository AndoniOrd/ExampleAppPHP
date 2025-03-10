<?php

namespace Database\Factories;

use App\Enums\TrackingOptions;
use App\Models\CampaignPlanning;
use App\Models\EmailTemplates;
use App\Models\MailingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CampaignPlanningFactory extends Factory
{
    protected $model = CampaignPlanning::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'email_template_id' => EmailTemplates::factory(),
            'mailing_list_id' => MailingList::factory(),
            'scheduled_time' => Carbon::now()->addDays(7),
            'time_zone' => fake()->timezone,
            'status_status_type' => fake()->randomElement(['draft', 'scheduled', 'processing', 'completed']),
            'creation_date' => Carbon::now(),
            'scheduled_by' => User::factory(),
            'send_from_email' => fake()->companyEmail(),
            'send_from_name' => fake()->company(),
            'reply_to_email' => fake()->safeEmail(),
            'tracking_options' => fake()->randomElement(TrackingOptions::cases()),
        ];
    }

    public function withSpecificTemplate(EmailTemplates $template): static
    {
        return $this->state(fn (array $attributes) => [
            'email_template_id' => $template->id,
        ]);
    }

    public function scheduledInPast(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_time' => Carbon::now()->subDays(7),
        ]);
    }

    public function withStatus(string $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status_status_type' => $status,
        ]);
    }
}