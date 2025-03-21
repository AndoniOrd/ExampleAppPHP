<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailSenderService
{
    /**
     * Send an email using the specified provider.
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param object $provider
     * @param array $options
     * @return bool
     */
    public function send($to, $subject, $content, $provider, array $options = [])
    {
        try {
            // Configure mail for the specific provider
            config([
                'mail.mailers.smtp.host' => $provider->host,
                'mail.mailers.smtp.port' => $provider->port,
                'mail.mailers.smtp.username' => $provider->username,
                'mail.mailers.smtp.password' => $provider->password,
                'mail.mailers.smtp.encryption' => $provider->encryption,
                'mail.from.address' => $options['from_email'] ?? $provider->from_email,
                'mail.from.name' => $options['from_name'] ?? $provider->from_name,
            ]);

            // Add tracking pixels if enabled
            $trackingOptions = $options['tracking_options'] ?? [];
            if (!empty($trackingOptions)) {
                // Add tracking pixel or other tracking mechanisms to content
                $content = $this->addTrackingToContent($content, $trackingOptions, $to);
            }

            // Send the email
            Mail::send([], [], function ($message) use ($to, $subject, $content, $options) {
                $message->to($to)
                    ->subject($subject)
                    ->setBody($content, 'text/html');
                
                // Set reply-to if provided
                if (!empty($options['reply_to'])) {
                    $message->replyTo($options['reply_to']);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'to' => $to,
                'provider' => $provider->name,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Add tracking mechanisms to email content
     *
     * @param string $content
     * @param array $trackingOptions
     * @param string $recipientEmail
     * @return string
     */
    private function addTrackingToContent($content, array $trackingOptions, $recipientEmail)
    {
        // This is a placeholder for your actual tracking implementation
        // You would typically add tracking pixels, UTM parameters to links, etc.
        
        // Example: Add open tracking pixel
        if (isset($trackingOptions['track_opens']) && $trackingOptions['track_opens']) {
            $trackingPixel = '<img src="' . route('email.track.open', [
                'email' => base64_encode($recipientEmail),
                'campaign' => $trackingOptions['campaign_id'] ?? 0,
                'ts' => time()
            ]) . '" width="1" height="1" alt="" style="display:none;">';
            
            // Add pixel before closing body tag
            $content = str_replace('</body>', $trackingPixel . '</body>', $content);
            
            // If no body tag, append to the end
            if (strpos($content, '</body>') === false) {
                $content .= $trackingPixel;
            }
        }
        
        // Example: Add click tracking to links
        if (isset($trackingOptions['track_clicks']) && $trackingOptions['track_clicks']) {
            // Replace links with tracking links
            $content = preg_replace_callback(
                '/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is',
                function ($matches) use ($recipientEmail, $trackingOptions) {
                    $originalUrl = $matches[1];
                    $linkText = $matches[2];
                    
                    // Skip mailto links
                    if (strpos($originalUrl, 'mailto:') === 0) {
                        return $matches[0];
                    }
                    
                    $trackingUrl = route('email.track.click', [
                        'email' => base64_encode($recipientEmail),
                        'campaign' => $trackingOptions['campaign_id'] ?? 0,
                        'url' => base64_encode($originalUrl),
                        'ts' => time()
                    ]);
                    
                    return '<a href="' . $trackingUrl . '">' . $linkText . '</a>';
                },
                $content
            );
        }
        
        return $content;
    }
}