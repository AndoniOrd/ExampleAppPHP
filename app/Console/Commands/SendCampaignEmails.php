<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendEmailJob;
use App\Services\EmailProviderService;
use App\Models\CampaignPlanning;
use App\Services\CampaignService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendCampaignEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-campaign {--campaign_id= : Specific campaign ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch emails from campaigns and queue them for sending';

    /**
     * @var EmailProviderService
     */
    protected $emailProviderService;

    /**
     * @var CampaignService
     */
    protected $campaignService;

    /**
     * Create a new command instance.
     *
     * @param EmailProviderService $emailProviderService
     * @param CampaignService $campaignService
     * @return void
     */
    public function __construct(
        EmailProviderService $emailProviderService,
        CampaignService $campaignService
    ) {
        parent::__construct();
        $this->emailProviderService = $emailProviderService;
        $this->campaignService = $campaignService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to process campaign emails...');
        
        // Check if we're processing a specific campaign
        $campaignId = $this->option('campaign_id');
        
        try {
            // Get ready campaigns (scheduled and time has come)
            $campaigns = $this->getCampaignsToProcess($campaignId);
            
            if ($campaigns->isEmpty()) {
                $this->warn('No campaigns ready to process at this time.');
                return 0;
            }
            
            foreach ($campaigns as $campaign) {
                $this->processCampaign($campaign);
            }
            
            $this->info('Campaign processing completed successfully.');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error processing campaigns: ' . $e->getMessage());
            Log::error('Campaign processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
    
    /**
     * Get campaigns that are ready to be processed
     *
     * @param int|null $campaignId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getCampaignsToProcess($campaignId = null)
    {
        $query = CampaignPlanning::query();
        
        // If campaign ID is provided, only get that specific campaign
        if ($campaignId) {
            $query->where('id', $campaignId);
        } else {
            // Otherwise, get all campaigns that are scheduled and ready to be sent
            $now = Carbon::now();
            $query->where('status_status_type', 'scheduled')
                  ->where('scheduled_time', '<=', $now);
        }
        
        return $query->get();
    }
    
    /**
     * Process a single campaign
     *
     * @param CampaignPlanning $campaign
     * @return void
     */
    protected function processCampaign(CampaignPlanning $campaign)
    {
        $this->info("Processing campaign: {$campaign->name} (ID: {$campaign->id})");
        
        // Get a random provider
        $provider = $this->emailProviderService->getRandomProvider();
        
        if (!$provider) {
            $this->error('No email providers available.');
            throw new \Exception('No email providers available');
        }
        
        $this->info('Selected provider: ' . $provider->name);
        
        // Get emails from mailing list
        $subscribers = $this->campaignService->getSubscribersFromMailingList($campaign->mailing_list_id);
        
        if (empty($subscribers)) {
            $this->warn("No subscribers found in mailing list ID: {$campaign->mailing_list_id}");
            return;
        }
        
        $this->info('Found ' . count($subscribers) . ' subscribers to process.');
        
        // Get email template content
        $emailContent = $this->campaignService->getEmailTemplateContent($campaign->email_template_id);
        
        if (!$emailContent) {
            $this->error("Could not load email template ID: {$campaign->email_template_id}");
            throw new \Exception("Could not load email template ID: {$campaign->email_template_id}");
        }
        
        // Update campaign status to 'sending'
        $campaign->status_status_type = 'sending';
        $campaign->save();
        
        // Queue jobs for each email
        $queuedCount = 0;
        foreach ($subscribers as $subscriber) {
            // Personalize email content for each subscriber
            $personalizedContent = $this->campaignService->personalizeEmailContent(
                $emailContent,
                $subscriber
            );
            
            // Create email data
            $emailData = [
                'email' => $subscriber['email'],
                'subject' => $campaign->name, // Or get this from template
                'content' => $personalizedContent,
                'campaign_id' => $campaign->id,
                'tracking_options' => $campaign->tracking_options,
                'from_email' => $campaign->send_from_email,
                'from_name' => $campaign->send_from_name,
                'reply_to' => $campaign->reply_to_email
            ];
            
            // Dispatch job
            SendEmailJob::dispatch($emailData, $provider)
                ->onQueue('emails');
            
            $this->line("Queued email to: {$subscriber['email']}");
            $queuedCount++;
        }
        
        $this->info("Successfully queued {$queuedCount} emails for campaign: {$campaign->name}");
    }
}