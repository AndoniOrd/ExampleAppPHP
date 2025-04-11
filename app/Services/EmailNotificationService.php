<?php

namespace App\Services;

use App\Models\User;
use App\Support\MailConfigHelper;
use Illuminate\Support\Facades\Log;
use App\Notifications\ImportErrorsNotification;
use Illuminate\Support\Facades\Notification;
use Exception;

class EmailNotificationService
{
    /**
     * Send import error notifications with proper error handling
     *
     * @param array $errorData The error data to include in the notification
     * @return bool Whether emails were successfully sent
     */
    public function sendImportErrorNotifications(array $errorData): bool
    {
        try {
            // Log the notification attempt
            $this->logNotificationAttempt($errorData);
            
            // Validate input structure
            if (!isset($errorData['existing'], $errorData['invalid'], $errorData['empty'])) {
                Log::error('Invalid error data structure', ['received_data' => array_keys($errorData)]);
                return false;
            }
    
            // Check mail configuration first using the helper
            MailConfigHelper::setupReliableMailConfig();
            if (!MailConfigHelper::isMailConfigured()) {
                Log::critical('Mail configuration invalid! Using fallback configuration');
                MailConfigHelper::setupFallbackMailConfig();
            }
    
            // Get admin recipients with fallback
            $recipients = $this->getAdminRecipients();
            
            if (empty($recipients)) {
                Log::warning('No recipients found for error notification');
                return false;
            }
            
            // Use the notifications framework to send to all recipients
            foreach ($recipients as $email) {
                try {
                    Notification::route('mail', $email)
                        ->notify(new ImportErrorsNotification($errorData));
                    
                    Log::info('Error notification sent to: ' . $email);
                } catch (Exception $e) {
                    Log::error('Failed to send notification to ' . $email, [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error('Error notification service failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Last resort - try with default mail configuration
            try {
                $fallback = config('mail.from.address', 'admin@example.com');
                Notification::route('mail', $fallback)
                    ->notify(new ImportErrorsNotification($errorData));
                
                Log::info('Sent fallback notification to ' . $fallback);
                return true;
            } catch (Exception $e2) {
                Log::critical('All notification attempts failed', [
                    'final_error' => $e2->getMessage()
                ]);
                return false;
            }
        }
    }
    
    /**
     * Log notification attempt details
     *
     * @param array $errorData
     */
    private function logNotificationAttempt(array $errorData): void
    {
        Log::info('Initiating import error notification process', [
            'existing_errors_count' => count($errorData['existing'] ?? []),
            'invalid_errors_count' => count($errorData['invalid'] ?? []),
            'empty_errors_count' => count($errorData['empty'] ?? []),
            'total_errors' => count($errorData['existing'] ?? []) + 
                             count($errorData['invalid'] ?? []) + 
                             count($errorData['empty'] ?? []),
            'time' => now()->toDateTimeString()
        ]);
    }
    
    /**
     * Get admin recipients
     * 
     * @return array Email addresses of admins
     */
    protected function getAdminRecipients(): array
    {
        $fallback = config('mail.from.address', 'admin@example.com');
        
        try {
            // First try to get admin users
            $adminEmails = User::where('is_admin', true)
                ->orWhere('role', 'admin')
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();
            
            // If no admins are found, check if we have a specific config
            if (empty($adminEmails)) {
                $configAdmins = config('mail.admin_emails', []);
                if (!empty($configAdmins)) {
                    if (is_string($configAdmins)) {
                        $adminEmails = [$configAdmins];
                    } elseif (is_array($configAdmins)) {
                        $adminEmails = $configAdmins;
                    }
                }
            }
            
            // Add fallback email if we still have no recipients
            if (empty($adminEmails)) {
                $adminEmails[] = $fallback;
            } else {
                // Add fallback as BCC
                $adminEmails[] = $fallback;
            }
            
            return array_unique(array_filter($adminEmails));
        } catch (Exception $e) {
            Log::warning('Failed to retrieve admin recipients: ' . $e->getMessage());
            return [$fallback];
        }
    }
}