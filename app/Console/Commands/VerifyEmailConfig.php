<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class VerifyEmailConfig extends Command
{
    protected $signature = 'email:verify';
    protected $description = 'Verify email configuration';

    public function handle()
    {
        $this->line('Current Email Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Driver', config('mail.default')],
                ['Host', config('mail.mailers.smtp.host')],
                ['Port', config('mail.mailers.smtp.port')],
                ['Encryption', config('mail.mailers.smtp.encryption')],
                ['Username', config('mail.mailers.smtp.username')],
                ['Password', config('mail.mailers.smtp.password') ? '*****' : ''],
                ['From Address', config('mail.from.address')],
                ['Fallback Recipient', config('mail.fallback_recipient')],
            ]
        );

        try {
            \Mail::raw('Test email', function ($message) {
                $message->to(config('mail.fallback_recipient'))
                        ->subject('Email Configuration Test');
            });
            $this->info('✓ Email configuration valid - test message sent');
        } catch (\Exception $e) {
            $this->error('✗ Email send failed: ' . $e->getMessage());
        }
    }
}