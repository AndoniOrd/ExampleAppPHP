<?php

// database/factories/ProviderFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = \App\Models\Provider::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'active' => $this->faker->boolean(),
            
            // SMTP data
            'smtp_host' => $this->faker->domainName(),
            'smtp_port' => 465,
            'smtp_encryption' => 'ssl',
            'smtp_username' => $this->faker->userName(),
            'smtp_password' => $this->faker->password(),
            
            // IMAP data
            'imap_host' => $this->faker->domainName(),
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'imap_username' => $this->faker->userName(),
            'imap_password' => $this->faker->password(),
        ];
    }
}
