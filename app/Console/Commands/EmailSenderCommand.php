<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\CampaignPlanning;
use App\Models\Provider;
use App\Helpers\MailConfigHelper;
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
        $activeProviders = Provider::where('active', true)
    ->whereNotNull('smtp_host')
    ->whereNotNull('smtp_port')
    ->get();

        $fallbackConfig = [
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_encryption' => config('mail.mailers.smtp.encryption'),
            'smtp_username' => config('mail.mailers.smtp.username'),
            'smtp_password' => config('mail.mailers.smtp.password'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        if ($activeProviders->isEmpty()) {
            $this->info("No active providers found, using .env configuration.");
            $providers = collect([(object) array_merge(['name' => 'Fallback'], $fallbackConfig)]);
        } else {
            $providers = $activeProviders;
        }

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
                $this->processCampaign($campaign, $providers, $totalEmailsDispatched);
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

    protected function processCampaign($campaign, $providers, &$totalEmailsDispatched)
    {
        $mailingList = $campaign->mailingList;
        if (!$mailingList) {
            $this->error("No mailing list found for campaign: {$campaign->name}");
            return;
        }

        $contacts = $mailingList->emailContacts()
            ->wherePivot('status', 'subscribed')
            ->wherePivotNull('unsubscribed_at')
            ->whereNotNull('email')
            ->where('email', 'regexp', '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$')
            ->get();

        if ($contacts->isEmpty()) {
            $this->warn("No valid contacts for campaign ID: {$campaign->id}");
            return;
        }

        $this->info("Dispatching {$contacts->count()} emails for campaign: {$campaign->name}");

        foreach ($contacts as $contact) {
            $retryCount = 0;
            $sent = false;

            do {
                $provider = $providers->first();

                if (empty($provider->smtp_host)) {
                    Log::error("Provider has no SMTP host configured", [
                        'provider_name' => $provider->name ?? 'Unknown'
                    ]);
                    $retryCount++;
                    continue;
                }

                $config = [
                    'host' => $provider->smtp_host,
                    'port' => (int) $provider->smtp_port,
                    'encryption' => $provider->smtp_encryption,
                    'username' => $provider->smtp_username,
                    'password' => $provider->smtp_password,
                ];

                if (\App\Support\MailConfigHelper::testConnection($config)) {
                    $providerData = [
                        'host' => $provider->smtp_host,
                        'port' => (int)$provider->smtp_port,
                        'encryption' => $provider->smtp_encryption,
                        'username' => $provider->smtp_username,
                        'password' => $provider->smtp_password,
                        'from_address' => $provider->from_address ?? config('mail.from.address'),
                        'from_name' => $provider->from_name ?? config('mail.from.name'),
                    ];

                    Log::info("Using provider", [
                        'provider_name' => $provider->name,
                        'host' => $providerData['host'],
                        'port' => $providerData['port']
                    ]);

                   // In processCampaign() method:
$emailTemplate = $campaign->emailTemplate;

SendEmailJob::dispatch([
    'campaign_id' => $campaign->id,
    'contact_id' => $contact->id,
    'provider' => $providerData,
    'contact_email' => $contact->email,
    'contact_name' => $contact->name,
    'subject' => $emailTemplate->subject_line ?? $campaign->name, // Use template subject
    'html_content' => $emailTemplate->html_content, // Include HTML content
    'plain_text_content' => $emailTemplate->plain_text_version,
    'data' => [
        'campaign_name' => $campaign->name,
        'contact_name' => $contact->name ?? 'Valued Customer',
        'contact_email' => $contact->email,
        'unsubscribe_link' => $campaign->tracking_options !== 'none'
            ? route('unsubscribe', [
                'contact' => $contact->id,
                'campaign' => $campaign->id
              ])
            : null
    ]
]);

                    $sent = true;
                    $totalEmailsDispatched++;
                } else {
                    $retryCount++;
                    Log::warning("Invalid provider {$provider->name}, retrying ({$retryCount}/3)");
                }

            } while (!$sent && $retryCount < 3);

            if (!$sent) {
                Log::error("Failed to send to {$contact->email} after 3 attempts");
            }
        }

        $this->info("ACTIVE PROVIDERS:");
        foreach ($providers as $p) {
            $this->info(" - {$p->name} ({$p->smtp_host}:{$p->smtp_port})");
        }

        $this->info("Processing {$contacts->count()} contacts...");

        $campaign->update(['status_type' => 'completed']);
    }

    public function debugMailingListContacts($mailingList)
    {
        $this->info("Debugging Mailing List: {$mailingList->name} (ID: {$mailingList->id})");

        $directContacts = $mailingList->emailContacts;
        $this->info("Direct contacts count: " . $directContacts->count());

        $relationshipMethodContacts = $mailingList->emailContacts();
        $this->info("Relationship method contacts query: " . $relationshipMethodContacts->toSql());

        $filteredContacts = $mailingList->emailContacts()
            ->wherePivot('status', 'subscribed')
            ->whereNotNull('email')
            ->where('email', 'LIKE', '%@%')
            ->toSql();

        $this->info("Filtered contacts SQL: " . $filteredContacts);

        $pivotEntries = \DB::table('email_contact_mailing_list')
            ->where('mailing_list_id', $mailingList->id)
            ->get();

        $this->info("Pivot table entries count: " . $pivotEntries->count());

        foreach ($pivotEntries as $entry) {
            $this->info("Pivot Entry Debug: " . json_encode($entry));
        }
    }
}
