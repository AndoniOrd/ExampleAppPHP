<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendEmailJob;
use App\Services\ActiveCampaignService;
use App\Services\EmailProviderService;

class SendCampaignEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch emails from Active Campaign and queue them for sending';

    /**
     * @var ActiveCampaignService
     */
    protected $activeCampaignService;

    /**
     * @var EmailProviderService
     */
    protected $emailProviderService;

    /**
     * Create a new command instance.
     *
     * @param ActiveCampaignService $activeCampaignService
     * @param EmailProviderService $emailProviderService
     * @return void
     */
    public function __construct(
        ActiveCampaignService $activeCampaignService,
        EmailProviderService $emailProviderService
    ) {
        parent::__construct();
        $this->activeCampaignService = $activeCampaignService;
        $this->emailProviderService = $emailProviderService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to fetch emails from Active Campaign...');
        
        // Get emails from Active Campaign
        $emails = $this->activeCampaignService->getEmailsFromList();
        
        if (empty($emails)) {
            $this->warn('No emails found in the Active Campaign list.');
            return 0;
        }
        
        $this->info('Found ' . count($emails) . ' emails to process.');
        
        // Get a random provider
        $provider = $this->emailProviderService->getRandomProvider();
        
        if (!$provider) {
            $this->error('No email providers available.');
            return 1;
        }
        
        $this->info('Selected provider: ' . $provider['name']);
        
        // Create jobs for each email
        foreach ($emails as $email) {
            SendEmailJob::dispatch($email, $provider)
                ->onQueue('emails');
            
            $this->line("Queued email to: {$email['email']}");
        }
        
        $this->info('All emails have been queued successfully.');
        
        return 0;
    }
}