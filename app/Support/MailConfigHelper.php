<?php

namespace App\Support;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MailConfigHelper
{
    /**
     * Set up reliable mail configuration for critical system emails
     *
     * @return void
     */
    public static function setupReliableMailConfig(): void
    {
        try {
            // Verify SMTP configuration exists
            if (empty(config('mail.mailers.smtp.host'))) {
                self::setupFallbackMailConfig();
                return;
            }
            
            // Test connectivity to mail server
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');
            
            $connection = @fsockopen($host, $port, $errno, $errstr, 5);
            if (!$connection) {
                Log::warning("Cannot connect to mail server: {$errstr} ({$errno})");
                self::setupFallbackMailConfig();
                return;
            }
            fclose($connection);
            
            // Set timeouts and retry options
            Config::set('mail.mailers.smtp.timeout', 30);
            Config::set('mail.mailers.smtp.verify_peer', false);
            
            Log::info('Mail configuration verified successfully', [
                'host' => $host,
                'port' => $port
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error verifying mail configuration: ' . $e->getMessage());
            self::setupFallbackMailConfig();
        }
    }
    
    /**
     * Setup fallback mail configuration using Mailtrap
     *
     * @return void
     */
    public static function setupFallbackMailConfig(): void  // Changed from private to public
    {
        Log::notice('Using fallback mail configuration (Mailtrap)');
        
        // Default Mailtrap credentials - replace with your actual credentials
        Config::set('mail.mailers.smtp.host', 'smtp.mailtrap.io');
        Config::set('mail.mailers.smtp.port', 2525);
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.username', 'your_mailtrap_username');
        Config::set('mail.mailers.smtp.password', 'your_mailtrap_password');
    }
    
    /**
     * Get an array of mail drivers in priority order
     *
     * @return array
     */
    public static function getMailDriversByPriority(): array
    {
        return [
            'smtp',     // Try SMTP first
            'mailgun',  // If configured, try Mailgun
            'ses',      // If configured, try Amazon SES
            'sendmail', // Try sendmail if available
            'log',      // Last resort - log emails instead of sending
        ];
    }
    
    /**
     * Try sending with fallback mail drivers
     *
     * @param callable $sendFunction Function that sends the email
     * @return bool Whether sending was successful
     */
    public static function sendWithFallbacks(callable $sendFunction): bool
    {
        $originalDriver = config('mail.default');
        $success = false;
        
        foreach (self::getMailDriversByPriority() as $driver) {
            // Skip drivers that aren't configured
            if ($driver !== 'log' && $driver !== 'smtp' && 
                empty(config("mail.mailers.{$driver}.key"))) {
                continue;
            }
            
            try {
                Config::set('mail.default', $driver);
                $sendFunction();
                $success = true;
                Log::info("Email sent successfully using driver: {$driver}");
                break;
            } catch (\Exception $e) {
                Log::warning("Failed to send email with driver: {$driver}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Restore original driver
        Config::set('mail.default', $originalDriver);
        
        return $success;
    }

    public static function verifyActiveMailConfiguration()
{
    try {
        $mailer = config('mail.default');
        $config = config("mail.mailers.{$mailer}");

        if (empty($config['host']) || empty($config['username'])) {
            Log::critical('Invalid mail configuration! Using fallback');
            self::setupFallbackMailConfig();
        }
    } catch (\Exception $e) {
        Log::error('Mail config verification failed: '.$e->getMessage());
    }
}

public static function isMailConfigured(): bool
{
    $required = [
        'host' => config('mail.mailers.smtp.host'),
        'port' => config('mail.mailers.smtp.port'),
        'username' => config('mail.mailers.smtp.username'),
        'password' => config('mail.mailers.smtp.password'),
    ];

    foreach ($required as $key => $value) {
        if (empty($value)) {
            Log::critical("Missing SMTP configuration: $key");
            return false;
        }
    }
    return true;
}

// In MailConfigHelper.php, add connection testing:
public static function testConnection($config)
{
    try {
        // Debug the connection parameters
        Log::info('Testing SMTP connection with:', [
            'host' => $config['host'],
            'port' => $config['port'],
            'encryption' => $config['encryption'] ?? 'none'
        ]);
        
        // Create transport with correct protocol
        $transport = new EsmtpTransport(
            $config['host'], 
            $config['port'], 
            $config['encryption'] === 'ssl' ? true : ($config['encryption'] === 'tls' ? false : null)
        );
        
        // Set credentials
        $transport->setUsername($config['username']);
        $transport->setPassword($config['password']);
        
        // Try to establish connection
        $transport->start();
        Log::info('SMTP connection successful');
        return true;
    } catch (Exception $e) {
        Log::error('SMTP connection test failed', [
            'error' => $e->getMessage(),
            'host' => $config['host'],
            'port' => $config['port'],
            'encryption' => $config['encryption'] ?? 'none'
        ]);
        return false;
    }
}
}