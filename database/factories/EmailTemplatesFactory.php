<?php

namespace Database\Factories;

use App\Models\EmailTemplates;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\EmailTemplateStatus;

class EmailTemplatesFactory extends Factory
{
    protected $model = EmailTemplates::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'subject_line' => $this->faker->sentence,
            'html_content' => $this->faker->randomHtml(),
            'plain_text_version' => $this->faker->paragraph,
            'creator' => 1,
            'creation_date' => now(),
            'last_updated_date' => now(),
            'category' => 'general',
            'status' => 'active',
            'preview_image_url' => $this->faker->imageUrl,
            'from_address' => $this->faker->email,
            'from_name' => $this->faker->name,
        ];
    }
    
}
