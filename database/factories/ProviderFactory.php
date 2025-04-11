<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = \App\Models\Provider::class;

    public function definition()
    {
        // Common valid configurations
        $smtpConfigs = [
            ['host' => 'smtp.mailtrap.io', 'port' => 2525, 'encryption' => 'tls'],
            ['host' => 'smtp.gmail.com', 'port' => 465, 'encryption' => 'ssl'],
            ['host' => 'smtp.office365.com', 'port' => 587, 'encryption' => 'tls'],
            ['host' => $this->faker->domainName(), 'port' => 465, 'encryption' => 'ssl'],
            ['host' => $this->faker->domainName(), 'port' => 587, 'encryption' => 'tls'],
        ];

        $imapConfigs = [
            ['host' => 'imap.mailtrap.io', 'port' => 993, 'encryption' => 'ssl'],
            ['host' => 'imap.gmail.com', 'port' => 993, 'encryption' => 'ssl'],
            ['host' => $this->faker->domainName(), 'port' => 993, 'encryption' => 'ssl'],
            ['host' => $this->faker->domainName(), 'port' => 143, 'encryption' => 'tls'],
        ];

        $smtp = $this->faker->randomElement($smtpConfigs);
        $imap = $this->faker->randomElement($imapConfigs);

        return [
            'name' => $this->faker->company(),
            'active' => $this->faker->boolean(80), // 80% chance of being active
            
            // SMTP Configuration
            'smtp_host' => $smtp['host'],
            'smtp_port' => $smtp['port'],
            'smtp_encryption' => $smtp['encryption'],
            'smtp_username' => $this->faker->userName(),
            'smtp_password' => $this->faker->password(),
            
            // IMAP Configuration
            'imap_host' => $imap['host'],
            'imap_port' => $imap['port'],
            'imap_encryption' => $imap['encryption'],
            'imap_username' => $this->faker->userName(),
            'imap_password' => $this->faker->password(),
        ];
    }

    public function mailtrap()
    {
        return $this->state([
            'smtp_host' => 'smtp.mailtrap.io',
            'smtp_port' => 2525,
            'smtp_encryption' => 'tls',
            'imap_host' => 'imap.mailtrap.io',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
        ]);
    }

    public function gmail()
    {
        return $this->state([
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 465,
            'smtp_encryption' => 'ssl',
            'imap_host' => 'imap.gmail.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
        ]);
    }
}