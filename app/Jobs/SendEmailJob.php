<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;

    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
    }

    public function handle()
{
    try {
        $provider = $this->emailData['provider'];

        // Verify and provide fallbacks for critical email fields
        $fromAddress = $provider['from_address'] ?? null;
        if (empty($fromAddress)) {
            // Fall back to .env configuration
            $fromAddress = config('mail.from.address');
            
            // If still empty, use a hard-coded fallback
            if (empty($fromAddress)) {
                $fromAddress = 'noreply@example.com';
                Log::warning('Using hardcoded fallback email address', ['job_id' => $this->job->getJobId()]);
            }
        }
        
        $fromName = $provider['from_name'] ?? config('mail.from.name', 'System');

        // Dynamically configure the mailer
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $provider['host'] ?? config('mail.mailers.smtp.host'),
            'port' => $provider['port'] ?? config('mail.mailers.smtp.port'),
            'encryption' => $provider['encryption'] ?? config('mail.mailers.smtp.encryption'),
            'username' => $provider['username'] ?? config('mail.mailers.smtp.username'),
            'password' => $provider['password'] ?? config('mail.mailers.smtp.password'),
            'timeout' => null,
            'auth_mode' => null,
        ]);
        
        Config::set('mail.from', [
            'address' => $fromAddress,
            'name' => $fromName,
        ]);

        // Send the email with verified from address
        Mail::send($this->emailData['template'], $this->emailData['data'], function ($message) use ($fromAddress, $fromName) {
            // Make sure recipient email exists
            if (empty($this->emailData['email'])) {
                throw new \Exception('Recipient email address is missing');
            }
            
            $message->to($this->emailData['email'], $this->emailData['name'] ?? null)
                    ->subject($this->emailData['subject'] ?? 'No Subject')
                    ->from($fromAddress, $fromName);
        });

        Log::channel('daily')->info('Email sent successfully', [
            'email' => $this->emailData['email']
        ]);
    } catch (\Exception $e) {
        Log::channel('daily')->error('Email sending failed', [
            'email' => $this->emailData['email'] ?? 'unknown',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        throw $e;
    }
}

    public function failed(\Throwable $exception)
{
    Log::channel('daily')->critical('Email job failed', [
        'email' => $this->emailData['email'],
        'exception' => $exception->getMessage(),
        'trace' => $exception->getTraceAsString()
    ]);
}
}
