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
        $activeProviders = Provider::where('active', true)->get();

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

        $contacts = $mailingList->contacts()
            ->wherePivot('status', 'subscribed')
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
                $provider = $providers->random();
                $config = [
                    'host' => $provider->smtp_host,
                    'port' => $provider->smtp_port,
                    'encryption' => $provider->smtp_encryption,  // Make sure this is correctly passed
                    'username' => $provider->smtp_username,
                    'password' => $provider->smtp_password,
                ];

                if (\App\Support\MailConfigHelper::testConnection($config)) {
                    $fromAddress = $provider->from_address ?? config('mail.from.address');

                    if (empty($fromAddress)) {
                        $this->warn("Provider {$provider->name} has no from address, skipping.");
                        break;
                    }

                    SendEmailJob::dispatch([
                        'campaign_id' => $campaign->id,
                        'contact_id' => $contact->id,
                        'provider' => [
                            'name' => $provider->name,
                            'host' => $provider->smtp_host,
                            'port' => $provider->smtp_port,
                            'encryption' => $provider->smtp_encryption,
                            'username' => $provider->smtp_username,
                            'password' => $provider->smtp_password,
                            'from_address' => $fromAddress,
                            'from_name' => $provider->from_name ?? config('mail.from.name'),
                        ],
                        'email' => $contact->email,
                        'name' => $contact->name ?? 'Subscriber',
                        'subject' => optional($campaign->emailTemplate)->subject ?? $campaign->name,
                        'template' => 'emails.campaign',
                        'data' => [
                            'campaign_name' => $campaign->name,
                            'contact_name' => $contact->name ?? 'Valued Customer',
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

        $campaign->update(['status_type' => 'completed']);
    }

    public function debugMailingListContacts($mailingList)
    {
        $this->info("Debugging Mailing List: {$mailingList->name} (ID: {$mailingList->id})");

        $directContacts = $mailingList->contacts;
        $this->info("Direct contacts count: " . $directContacts->count());

        $relationshipMethodContacts = $mailingList->contacts();
        $this->info("Relationship method contacts query: " . $relationshipMethodContacts->toSql());

        $filteredContacts = $mailingList->contacts()
            ->wherePivot('status', 'subscribed')
            ->whereNotNull('email')
            ->where('email', 'LIKE', '%@%')
            ->toSql();

        $this->info("Filtered contacts SQL: " . $filteredContacts);

        $pivotEntries = \DB::table('contact_mailing_list')
            ->where('mailing_list_id', $mailingList->id)
            ->get();

        $this->info("Pivot table entries count: " . $pivotEntries->count());

        foreach ($pivotEntries as $entry) {
            $this->info("Pivot Entry Debug: " . json_encode($entry));
        }
    }
}
