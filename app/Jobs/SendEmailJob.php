<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;

    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
    }

    public function handle()
    {
        try {
            $provider = $this->emailData['provider'] ?? [];

            // Validate critical SMTP settings
            $this->validateSmtpConfig($provider);

            // Configure mailer
            $this->configureMailer($provider);

            // Send email
            $this->sendEmail();

            Log::channel('daily')->info('Email sent successfully', [
                'contact_email' => $this->emailData['contact_email'],
                'using_host' => config('mail.mailers.smtp.host')
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error('Email sending failed', [
                'contact_email' => $this->emailData['contact_email'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    private function validateSmtpConfig(array $provider): void
    {
        $host = $provider['host'] ?? config('mail.mailers.smtp.host');
        $username = $provider['username'] ?? config('mail.mailers.smtp.username');
        $password = $provider['password'] ?? config('mail.mailers.smtp.password');

        if (empty($host)) {
            throw new \InvalidArgumentException('SMTP host is required');
        }

        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('SMTP credentials are required');
        }
    }

    private function configureMailer(array $provider): void
    {
        $host = $provider['host'] ?? config('mail.mailers.smtp.host');
        $port = isset($provider['port']) ? (int)$provider['port'] : (int)config('mail.mailers.smtp.port', 587);
        $encryption = $provider['encryption'] ?? config('mail.mailers.smtp.encryption', 'tls');
        $username = $provider['username'] ?? config('mail.mailers.smtp.username');
        $password = $provider['password'] ?? config('mail.mailers.smtp.password');

        Config::set([
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => $port,
            'mail.mailers.smtp.encryption' => $encryption,
            'mail.mailers.smtp.username' => $username,
            'mail.mailers.smtp.password' => $password,
            'mail.mailers.smtp.timeout' => 30,
        ]);

        // Set from address
        $fromAddress = $provider['from_address'] ?? config('mail.from.address');
        $fromName = $provider['from_name'] ?? config('mail.from.name', 'System');

        if (empty($fromAddress)) {
            throw new \InvalidArgumentException('From address is required');
        }

        Config::set([
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName
        ]);

        Log::info('Mail configuration set', [
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'from' => $fromAddress
        ]);

        app()->forgetInstance('mailer');
        app()->forgetInstance('swift.mailer');
    }

    private function sendEmail(): void
    {
        $hasHtml = !empty($this->emailData['html_content']);
        $hasText = !empty($this->emailData['plain_text_content']);

        if (!$hasHtml && !$hasText) {
            throw new \InvalidArgumentException('Email must have either HTML or plain text content');
        }

        $mailer = Mail::mailer('smtp');

        if ($hasHtml && $hasText) {
            $mailer->send([], [], function (Message $message) {
                $this->buildMessage($message);
                $message->html($this->emailData['html_content'])
                    ->text($this->emailData['plain_text_content']);
            });
        } elseif ($hasHtml) {
            $mailer->html($this->emailData['html_content'], function (Message $message) {
                $this->buildMessage($message);
            });
        } else {
            $mailer->raw($this->emailData['plain_text_content'], function (Message $message) {
                $this->buildMessage($message);
            });
        }

        Log::info('Email sending attempt completed', [
            'contact_email' => $this->emailData['contact_email'],
            'has_html' => $hasHtml,
            'has_text' => $hasText,
            'mailer_used' => 'smtp'
        ]);
    }

    private function buildMessage(Message $message): void
    {
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $message->to($this->emailData['contact_email'], $this->emailData['contact_name'] ?? null)
            ->subject($this->emailData['subject'] ?? 'No Subject')
            ->from($fromAddress, $fromName);
    }

    public function failed(\Throwable $exception)
    {
        Log::channel('daily')->critical('Email job failed', [
            'contact_email' => $this->emailData['contact_email'] ?? 'unknown',
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
