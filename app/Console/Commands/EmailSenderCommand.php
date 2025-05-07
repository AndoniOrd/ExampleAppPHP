<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\CampaignPlanning;
use App\Models\Provider;
use App\Helpers\MailConfigHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmailSenderCommand extends Command
{
    protected $signature   = 'emails:send';
    protected $description = 'Send scheduled campaign emails';

    public function handle()
    {
        // 1. Cargamos proveedores o fallback
        $providers = Provider::where('active', true)
            ->whereNotNull('smtp_host')
            ->whereNotNull('smtp_port')
            ->get()
            ->whenEmpty(fn() => collect([(object)[
                'name'            => 'Fallback',
                'smtp_host'       => config('mail.mailers.smtp.host'),
                'smtp_port'       => config('mail.mailers.smtp.port'),
                'smtp_encryption' => config('mail.mailers.smtp.encryption'),
                'smtp_username'   => config('mail.mailers.smtp.username'),
                'smtp_password'   => config('mail.mailers.smtp.password'),
                'from_address'    => config('mail.from.address'),
                'from_name'       => config('mail.from.name'),
            ]]));

        // 2. Obtenemos campañas pendientes
        $campaigns = CampaignPlanning::with(['emailTemplate', 'mailingList.emailContacts'])
            ->where('status_type', 'scheduled')
            ->where('scheduled_time', '<=', Carbon::now())
            ->get();

        if ($campaigns->isEmpty()) {
            $this->warn('No campaigns ready to send.');
            return Command::SUCCESS;
        }

        $this->info("Found {$campaigns->count()} campaigns");
        $grandTotal = 0;

        foreach ($campaigns as $campaign) {
            // Mark as processing
            $campaign->update(['status_type' => 'processing']);

            // Process and track counts
            $sent = $this->processCampaign($campaign, $providers);
            $grandTotal += $sent;

            // Mark as completed
            $campaign->update(['status_type' => 'completed']);
        }

        $this->info("Total emails dispatched: {$grandTotal}");
        return Command::SUCCESS;
    }

    protected function processCampaign($campaign, $providers): int
    {
        $contacts = $campaign->mailingList
            ->emailContacts()
            ->wherePivot('status','subscribed')
            ->wherePivotNull('unsubscribed_at')
            ->whereNotNull('email')
            ->get();

        if ($contacts->isEmpty()) {
            $this->warn("No valid contacts for campaign {$campaign->id}");
            return 0;
        }

        $total = $contacts->count();
        // Inicializamos en cache
        Cache::put("campaign:{$campaign->id}:total", $total, now()->addHours(2));
        Cache::put("campaign:{$campaign->id}:sent",  0,     now()->addHours(2));

        $this->info("Dispatching {$total} emails for campaign: {$campaign->name}");

        $dispatched = 0;

        foreach ($contacts as $contact) {
            foreach ($providers as $prov) {
                // Test de conexión SMTP
                if (! MailConfigHelper::testConnection([
                    'host'       => $prov->smtp_host,
                    'port'       => $prov->smtp_port,
                    'username'   => $prov->smtp_username,
                    'password'   => $prov->smtp_password,
                    'encryption' => $prov->smtp_encryption,
                ])) {
                    Log::warning("Provider {$prov->name} failed, trying next");
                    continue;
                }

                // Encolamos el envío
                SendEmailJob::dispatch([
                    'campaign_id'        => $campaign->id,
                    'contact_id'         => $contact->id,
                    'provider'           => [
                        'host'         => $prov->smtp_host,
                        'port'         => $prov->smtp_port,
                        'encryption'   => $prov->smtp_encryption,
                        'username'     => $prov->smtp_username,
                        'password'     => $prov->smtp_password,
                        'from_address' => $prov->from_address,
                        'from_name'    => $prov->from_name,
                    ],
                    'contact_email'      => $contact->email,
                    'contact_name'       => $contact->name,
                    'subject'            => $campaign->emailTemplate->subject_line ?? $campaign->name,
                    'html_content'       => $campaign->emailTemplate->html_content,
                    'plain_text_content' => $campaign->emailTemplate->plain_text_version,
                    'data'               => [
                        'campaign_name'    => $campaign->name,
                        'contact_name'     => $contact->name,
                        'unsubscribe_link' => route('unsubscribe', [
                            'contact'  => $contact->id,
                            'campaign' => $campaign->id,
                        ]),
                    ],
                ]);

                $dispatched++;
                // Actualizamos cache de enviados
                Cache::increment("campaign:{$campaign->id}:sent");
                break; // salimos bucle providers
            }
        }

        return $dispatched;
    }
}