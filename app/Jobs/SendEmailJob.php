<?php

namespace App\Jobs;

use App\Mail\CampaignEmail;
use App\Mail\CampaignMail;
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

    /**
     * The email data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Get the provider information
            $provider = $this->data['provider'];
            
            // Configure the mail settings for this specific email
            Config::set('mail.default', $provider['mailer']);
            Config::set('mail.mailers.smtp.host', $provider['host']);
            Config::set('mail.mailers.smtp.port', $provider['port']);
            Config::set('mail.mailers.smtp.username', $provider['username']);
            Config::set('mail.mailers.smtp.password', $provider['password']);
            Config::set('mail.from.address', $provider['from_address']);
            Config::set('mail.from.name', $provider['from_name']);
            
            // Send the email
            Mail::to($this->data['email'])
                ->send(new CampaignEmail([
                    'subject' => $this->data['subject'],
                    'template' => $this->data['template'],
                    'data' => $this->data['data'],
                ]));
                
            // Log the successful send
            Log::info('Email sent successfully', [
                'campaign_id' => $this->data['campaign_id'],
                'contact_id' => $this->data['contact_id'],
                'provider' => $provider['name'],
            ]);
            
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Failed to send email', [
                'campaign_id' => $this->data['campaign_id'] ?? null,
                'contact_id' => $this->data['contact_id'] ?? null,
                'provider' => $provider['name'] ?? null,
                'error' => $e->getMessage(),
            ]);
            
            // You could retry the job here if needed
            $this->release(30); // Release the job back to the queue after 30 seconds
        }
    }
}