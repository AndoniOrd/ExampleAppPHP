<?php

namespace App\Jobs;

use App\Events\EmailProgressUpdated;
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

    protected $emailData;
    protected $totalEmails;
    protected $campaignId;

    public function __construct(array $emailData, int $totalEmails, int $campaignId)
    {
        $this->emailData = $emailData;
        $this->totalEmails = $totalEmails;
        $this->campaignId = $campaignId;
    }

    public function handle()
    {
        try {
            // Configurar SMTP dinámico
            $provider = $this->emailData['provider'] ?? [];
            
            Config::set('mail.mailers.smtp.host', $provider['host'] ?? config('mail.mailers.smtp.host'));
            Config::set('mail.mailers.smtp.port', $provider['port'] ?? config('mail.mailers.smtp.port'));
            Config::set('mail.mailers.smtp.encryption', $provider['encryption'] ?? config('mail.mailers.smtp.encryption'));
            Config::set('mail.mailers.smtp.username', $provider['username'] ?? config('mail.mailers.smtp.username'));
            Config::set('mail.mailers.smtp.password', $provider['password'] ?? config('mail.mailers.smtp.password'));
            
            $fromAddress = $provider['from_address'] ?? config('mail.from.address');
            $fromName = $provider['from_name'] ?? config('mail.from.name');

            // Enviar un solo email
            Mail::send([], [], function ($message) use ($fromAddress, $fromName) {
                $message
                    ->to($this->emailData['contact_email'], $this->emailData['contact_name'])
                    ->subject($this->emailData['subject'])
                    ->from($fromAddress, $fromName)
                    ->html($this->emailData['html_content'])
                    ->text($this->emailData['plain_text_content']);
            });

            // Emitir evento de progreso
            $percent = (int) (($this->attempts() / $this->totalEmails) * 100);
            broadcast(new EmailProgressUpdated($this->campaignId, $percent));

            Log::info('Email sent', [
                'contact_email' => $this->emailData['contact_email'],
                'progress' => $percent
            ]);

        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::critical('Email job failed', [
            'campaign_id' => $this->campaignId,
            'exception' => $exception->getMessage()
        ]);
    }
}