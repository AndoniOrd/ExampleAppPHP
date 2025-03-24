<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\CampaignPlanning;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EmailSenderCommand extends Command
{
    protected $signature = 'emails:send';
    protected $description = 'Send scheduled campaign emails using Mailtrap';

    public function handle()
    {
        // Mailtrap configuration
        $provider = [
            'name' => 'mailtrap',
            'mailer' => 'smtp',
            'host' => 'smtp.mailtrap.io',
            'port' => 2525,
            'username' => '46106c496adb8a',
            'password' => '3143df002d610d',
            'from_address' => 'borjaahedo@gmail.com',
            'from_name' => config('app.name', 'Laravel'),
        ];

        $this->info("Using provider: {$provider['name']}");

        // Get campaigns marked as scheduled
        $campaigns = CampaignPlanning::where('status_status_type', 'scheduled')
            ->get()
            ->filter(function ($campaign) {
                // Convert scheduled_time to campaign's timezone
                $scheduledTime = $campaign->scheduled_time->timezone($campaign->time_zone);
                $now = Carbon::now($campaign->time_zone);
                
                Log::info("Campaign {$campaign->id} check", [
                    'scheduled' => $scheduledTime,
                    'now' => $now,
                    'comparison' => $scheduledTime->lte($now)
                ]);
                
                return $scheduledTime->lte($now);
            });

        if ($campaigns->isEmpty()) {
            $this->warn('No active campaigns found.');
            Log::info('No campaigns ready for sending', [
                'current_time_utc' => now()->toDateTimeString(),
                'timezone' => config('app.timezone')
            ]);
            return Command::SUCCESS;
        }

        $this->info("Found {$campaigns->count()} campaigns ready to send");

        $totalEmailsDispatched = 0;

        foreach ($campaigns as $campaign) {
            try {
                $campaign->update(['status_status_type' => 'processing']);
                $this->info("Processing campaign: {$campaign->name}");

                // Get valid contacts through mailing list
                $contacts = $campaign->mailingList->contacts()
                ->wherePivot('status', 'subscribed')
                ->whereNotNull('email')  // or whatever your column is named
                ->where('email', 'LIKE', '%@%')
                ->get();
            
            if ($contacts->isEmpty()) {
                $this->warn("No valid contacts for campaign ID: {$campaign->id}");
                continue;
            }

                $this->info("Dispatching {$contacts->count()} emails");

                foreach ($contacts as $contact) {
                    SendEmailJob::dispatch([
                        'campaign_id' => $campaign->id,
                        'contact_id' => $contact->id,
                        'provider' => $provider,
                        'email' => $contact->email,
                        'name' => $contact->first_name ?? 'Subscriber',
                        'subject' => $campaign->emailTemplate->subject ?? $campaign->name,
                        'template' => 'emails.campaign',
                        'data' => [
                            'campaign_name' => $campaign->name,
                            'contact_name' => $contact->first_name ?? 'Valued Customer',
                        ]
                    ]);

                    $totalEmailsDispatched++;
                }

                $campaign->update(['status_status_type' => 'completed']);

            } catch (\Exception $e) {
                Log::error("Campaign failed: {$e->getMessage()}", [
                    'campaign_id' => $campaign->id,
                    'trace' => $e->getTraceAsString()
                ]);
                $campaign->update(['status_status_type' => 'failed']);
                $this->error("Error processing campaign: {$e->getMessage()}");
            }
        }

        $this->info("Total emails dispatched: {$totalEmailsDispatched}");
        return Command::SUCCESS;
    }
}