<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\TestEmail;
use App\Support\MailConfigHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email?}';
    protected $description = 'Test email configuration by sending a test email';

    public function handle()
    {
        $recipient = $this->argument('email') ?? config('mail.admin_email', 'borjaahedo@gmail.com');
        
        $this->info('Testing mail configuration...');
        $this->info("Current settings:");
        $this->table(['Setting', 'Value'], [
            ['Driver', config('mail.default')],
            ['Host', config('mail.mailers.smtp.host')],
            ['Port', config('mail.mailers.smtp.port')],
            ['Username', config('mail.mailers.smtp.username')],
            ['From', config('mail.from.address')],
        ]);
        
        $this->info("Sending test email to: {$recipient}");
        
        try {
            // Use the MailConfigHelper to ensure proper configuration
            MailConfigHelper::setupReliableMailConfig();
            
            // Send a test email
            Mail::to($recipient)->send(new TestEmail([
                'name' => 'Test User',
                'message' => 'This is a test email to verify mail configuration'
            ]));
            
            $this->info('✓ Email sent successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Mail sending failed: " . $e->getMessage());
            
            // Try with fallback configuration
            $this->warn('Trying with fallback configuration...');
            try {
                MailConfigHelper::setupFallbackMailConfig();
                
                Mail::to($recipient)->send(new TestEmail([
                    'name' => 'Test User',
                    'message' => 'This is a fallback test email'
                ]));
                
                $this->info('✓ Email sent with fallback configuration');
                return Command::SUCCESS;
            } catch (\Exception $e2) {
                $this->error("✗ Fallback mail sending also failed: " . $e2->getMessage());
                $this->error("Check your mail configuration in .env file");
                
                Log::critical('Mail test command failed with both primary and fallback configurations', [
                    'primary_error' => $e->getMessage(),
                    'fallback_error' => $e2->getMessage()
                ]);
                
                return Command::FAILURE;
            }
        }
    }
}