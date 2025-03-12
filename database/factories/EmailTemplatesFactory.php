<?php

namespace Database\Factories;

use App\Models\EmailTemplates;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\EmailTemplateStatus;

class EmailTemplatesFactory extends Factory
{
    protected $model = EmailTemplates::class;

    public function definition()
    {
        return [
            'name'                 => $this->faker->sentence(3),
            'description'          => $this->faker->paragraph,
            'subject_line'         => $this->faker->sentence,
            'html_content'         => $this->faker->randomHtml(2, 3),
            'plain_text_version'   => $this->faker->text,
            // Create a related user and use its id as the creator
            'creator'              => User::factory()->create()->id,
            'creation_date'        => $this->faker->date(),
            'last_updated_date'    => $this->faker->date(),
            'category'             => $this->faker->word,
            // Assuming your EmailTemplateStatus enum has an ACTIVE option; adjust as needed
            'status'               => EmailTemplateStatus::ACTIVE,
            'preview_image_url'    => $this->faker->imageUrl(640, 480, 'abstract', true),
        ];
    }
}