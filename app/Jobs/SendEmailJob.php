<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
            // Extensive logging for debugging
            Log::channel('daily')->info('Sending Email', [
                'campaign_id' => $this->emailData['campaign_id'],
                'contact_id' => $this->emailData['contact_id'],
                'email' => $this->emailData['email']
            ]);

            // Use Laravel's Mail facade with more comprehensive configuration
            Mail::send('emails.campaign', $this->emailData['data'], function($message) {
                $message->to($this->emailData['email'], $this->emailData['name'])
                    ->subject($this->emailData['subject'])
                    ->from(
                        $this->emailData['provider']['from_address'], 
                        $this->emailData['provider']['from_name']
                    );
            });

            Log::channel('daily')->info('Email sent successfully', [
                'email' => $this->emailData['email']
            ]);
        } catch (\Exception $e) {
            // Comprehensive error logging
            Log::channel('daily')->error('Email sending failed', [
                'email' => $this->emailData['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Optionally re-throw to trigger job failure
            throw $e;
        }
    }
}