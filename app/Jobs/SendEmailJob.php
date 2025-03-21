<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailSenderService;
use Illuminate\Support\Facades\Log;
use App\Services\CampaignService;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The email data.
     *
     * @var array
     */
    protected $emailData;

    /**
     * The email provider data.
     *
     * @var object
     */
    protected $provider;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param array $emailData
     * @param object $provider
     * @return void
     */
    public function __construct(array $emailData, $provider)
    {
        $this->emailData = $emailData;
        $this->provider = $provider;
    }

    /**
     * Execute the job.
     *
     * @param EmailSenderService $senderService
     * @param CampaignService $campaignService
     * @return void
     */
    public function handle(EmailSenderService $senderService, CampaignService $campaignService)
    {
        try {
            Log::info('Attempting to send email', [
                'email' => $this->emailData['email'],
                'provider' => $this->provider->name,
                'campaign_id' => $this->emailData['campaign_id'] ?? null
            ]);

            // Configure tracking options if available
            $trackingOptions = [];
            if (isset($this->emailData['tracking_options'])) {
                $trackingOptions = json_decode($this->emailData['tracking_options'], true) ?? [];
            }

            // Send the email
            $result = $senderService->send(
                $this->emailData['email'],
                $this->emailData['subject'],
                $this->emailData['content'],
                $this->provider,
                [
                    'from_email' => $this->emailData['from_email'] ?? null,
                    'from_name' => $this->emailData['from_name'] ?? null,
                    'reply_to' => $this->emailData['reply_to'] ?? null,
                    'tracking_options' => $trackingOptions
                ]
            );

            if ($result) {
                Log::info('Email sent successfully', [
                    'email' => $this->emailData['email'],
                    'campaign_id' => $this->emailData['campaign_id'] ?? null
                ]);
                
                // Log this email as sent in your email_sent_logs table if needed
                // $this->logEmailSent();
            } else {
                throw new \Exception('Email sending failed');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'email' => $this->emailData['email'],
                'provider' => $this->provider->name,
                'campaign_id' => $this->emailData['campaign_id'] ?? null,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Email job failed', [
            'email' => $this->emailData['email'] ?? 'unknown',
            'campaign_id' => $this->emailData['campaign_id'] ?? null,
            'error' => $exception->getMessage()
        ]);
        
        // You might want to log this failure in a dedicated table
        // or notify someone about the failure
    }
}