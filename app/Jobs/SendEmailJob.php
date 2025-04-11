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
        // Make sure we validate provider data on job creation
        if (empty($emailData['provider']['host'])) {
            Log::critical('Missing host in provider configuration when creating job');
            
            // Set default from config
            $emailData['provider']['host'] = config('mail.mailers.smtp.host');
            
            // If still empty, use a fallback
            if (empty($emailData['provider']['host'])) {
                $emailData['provider']['host'] = 'smtp.gmail.com';
            }
        }
        
        $this->emailData = $emailData;
    }

    public function handle()
    {
        try {
            // Get provider data or use an empty array if not set
            $provider = $this->emailData['provider'] ?? [];
            
            // Validate critical connection info
            $host = $provider['host'] ?? null;
            if (empty($host)) {
                // Get from .env config
                $host = config('mail.mailers.smtp.host');
                
                // If still empty, use Gmail as fallback
                if (empty($host)) {
                    $host = 'smtp.gmail.com';
                }
                
                Log::warning('Missing host in provider config, using fallback', ['host' => $host]);
            }
            
            // Ensure port is set and valid
            $port = isset($provider['port']) ? (int)$provider['port'] : null;
            if (empty($port) || $port <= 0) {
                $port = (int)config('mail.mailers.smtp.port', 587);
            }
            
            // Get encryption
            $encryption = $provider['encryption'] ?? config('mail.mailers.smtp.encryption', 'tls');
            
            // Log what we're using for debugging
            Log::info('Setting mail transport configuration', [
                'host' => $host,
                'port' => $port,
                'encryption' => $encryption
            ]);
            
            // Configure mailer with verified values
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $host);
            Config::set('mail.mailers.smtp.port', $port);
            Config::set('mail.mailers.smtp.encryption', $encryption);
            Config::set('mail.mailers.smtp.username', $provider['username'] ?? config('mail.mailers.smtp.username'));
            Config::set('mail.mailers.smtp.password', $provider['password'] ?? config('mail.mailers.smtp.password'));
            Config::set('mail.mailers.smtp.timeout', 30);
            
            // Ensure from address is set
            $fromAddress = $provider['from_address'] ?? null;
            if (empty($fromAddress)) {
                $fromAddress = config('mail.from.address');
                if (empty($fromAddress)) {
                    $fromAddress = 'noreply@example.com';
                }
            }
            
            $fromName = $provider['from_name'] ?? config('mail.from.name', 'System');
            
            // Set from address
            Config::set('mail.from.address', $fromAddress);
            Config::set('mail.from.name', $fromName);
            
            // Actually send the email
            Mail::send($this->emailData['template'], $this->emailData['data'], function ($message) use ($fromAddress, $fromName) {
                $message->to(
                    $this->emailData['contact_email'],
                    $this->emailData['contact_name'] ?? null
                )
                ->subject($this->emailData['subject'] ?? 'No Subject')
                ->from($fromAddress, $fromName);
            });

            Log::channel('daily')->info('Email sent successfully', [
                'contact_email' => $this->emailData['contact_email'],
                'using_host' => config('mail.mailers.smtp.host') // Log what host was actually used
            ]);
        } catch (\Exception $e) {
            Log::channel('daily')->error('Email sending failed', [
                'contact_email' => $this->emailData['contact_email'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption')
                ]
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::channel('daily')->critical('Email job failed', [
            'contact_email' => $this->emailData['contact_email'] ?? 'unknown',
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'config' => [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption')
            ]
        ]);
    }
}