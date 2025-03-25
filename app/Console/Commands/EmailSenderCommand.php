<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\CampaignPlanning;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class EmailSenderCommand extends Command
{
    protected $signature = 'emails:send';
    protected $description = 'Send scheduled campaign emails';

    public function handle()
    {
        // Use environment configuration for SMTP settings
        $provider = [
            'name' => config('mail.default', 'mailtrap'),
            'mailer' => config('mail.mailers.smtp.transport', 'smtp'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name', config('app.name')),
        ];

        $this->info("Using email provider: {$provider['name']}");

        // Retrieve scheduled campaigns more robustly
        $campaigns = CampaignPlanning::with(['mailingList.contacts'])
            ->where('status_type', 'scheduled')
            ->where('scheduled_time', '<=', Carbon::now())
            ->get();

        if ($campaigns->isEmpty()) {
            $this->warn('No campaigns ready to send.');
            return Command::SUCCESS;
        }

        $this->info("Found {$campaigns->count()} campaigns to process");
        $totalEmailsDispatched = 0;

        foreach ($campaigns as $campaign) {
            try {
                $campaign->update(['status_type' => 'processing']);
                $this->processCampaign($campaign, $provider, $totalEmailsDispatched);
            } catch (\Exception $e) {
                Log::error("Campaign processing failed", [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage()
                ]);
                $campaign->update(['status_type' => 'failed']);
                $this->error("Error processing campaign: {$e->getMessage()}");
            }
        }

        $this->info("Total emails dispatched: {$totalEmailsDispatched}");
        return Command::SUCCESS;
    }

    protected function processCampaign($campaign, $provider, &$totalEmailsDispatched)
    {
        $mailingList = $campaign->mailingList;
        if (!$mailingList) {
            $this->error("No mailing list found for campaign: {$campaign->name}");
            return;
        }

        $contacts = $mailingList->contacts()
            ->wherePivot('status', 'subscribed')
            ->whereNotNull('email')
            ->where('email', 'regexp', '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}$')
            ->get();

        if ($contacts->isEmpty()) {
            $this->warn("No valid contacts for campaign ID: {$campaign->id}");
            return;
        }

        $this->info("Dispatching {$contacts->count()} emails for campaign: {$campaign->name}");

        foreach ($contacts as $contact) {
            SendEmailJob::dispatch([
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'provider' => $provider,
                'email' => $contact->email,
                'name' => $contact->name ?? 'Subscriber',
                'subject' => optional($campaign->emailTemplate)->subject ?? $campaign->name,
                'template' => 'emails.campaign',
                'data' => [
                    'campaign_name' => $campaign->name,
                    'contact_name' => $contact->name ?? 'Valued Customer',
                ]
            ]);

            $totalEmailsDispatched++;
        }

        $campaign->update(['status_type' => 'completed']);
    }

    public function debugMailingListContacts($mailingList)
{
    // Verbose debugging of mailing list and contacts
    $this->info("Debugging Mailing List: {$mailingList->name} (ID: {$mailingList->id})");

    // Check direct relationship query
    $directContacts = $mailingList->contacts;
    $this->info("Direct contacts count: " . $directContacts->count());

    // Check relationship method
    $relationshipMethodContacts = $mailingList->contacts();
    $this->info("Relationship method contacts query: " . $relationshipMethodContacts->toSql());

    // Attempt to fetch contacts with verbose conditions
    $filteredContacts = $mailingList->contacts()
        ->wherePivot('status', 'subscribed')
        ->whereNotNull('email')
        ->where('email', 'LIKE', '%@%')
        ->toSql();
    
    $this->info("Filtered contacts SQL: " . $filteredContacts);

    // Manually check pivot table entries
    $pivotEntries = \DB::table('contact_mailing_list')
        ->where('mailing_list_id', $mailingList->id)
        ->get();
    
    $this->info("Pivot table entries count: " . $pivotEntries->count());
    
    foreach ($pivotEntries as $entry) {
        $this->info("Pivot Entry Debug: " . json_encode($entry));
    }
}
}