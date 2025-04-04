<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ImportErrorsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The error data.
     *
     * @var array
     */
    protected $errorData;

    /**
     * Create a new notification instance.
     *
     * @param array $errorData
     * @return void
     */
    public function __construct(array $errorData)
    {
        $this->errorData = $errorData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        try {
            $mailMessage = (new MailMessage)
                ->subject('⚠️ Email Contacts Import Error Report')
                ->greeting('Import Error Report')
                ->line('The system has detected errors during the email contacts import process.')
                ->line('Here is a summary of the issues:');
            
            $totalErrors = 0;
            
            // Add sections for each error type
            if (!empty($this->errorData['existing'])) {
                $count = count($this->errorData['existing']);
                $totalErrors += $count;
                $mailMessage->line("• {$count} existing email(s) were found");
                
                // Add the first few existing emails
                if ($count > 0) {
                    $emails = array_slice($this->errorData['existing'], 0, 5);
                    $emailList = array_map(function($item) {
                        return '- ' . $item['email'] . ' (Row ' . $item['row'] . ')';
                    }, $emails);
                    
                    $mailMessage->line(implode("\n", $emailList));
                    
                    if ($count > 5) {
                        $mailMessage->line('... and ' . ($count - 5) . ' more');
                    }
                }
            }
            
            if (!empty($this->errorData['invalid'])) {
                $count = count($this->errorData['invalid']);
                $totalErrors += $count;
                $mailMessage->line("• {$count} invalid email(s) were found");
                
                // Add the first few invalid emails
                if ($count > 0) {
                    $emails = array_slice($this->errorData['invalid'], 0, 5);
                    $emailList = array_map(function($item) {
                        return '- ' . $item['email'] . ' (Row ' . $item['row'] . '): ' . $item['reason'];
                    }, $emails);
                    
                    $mailMessage->line(implode("\n", $emailList));
                    
                    if ($count > 5) {
                        $mailMessage->line('... and ' . ($count - 5) . ' more');
                    }
                }
            }
            
            if (!empty($this->errorData['empty'])) {
                $count = count($this->errorData['empty']);
                $totalErrors += $count;
                $mailMessage->line("• {$count} row(s) with missing required data");
            }
            
            // Add timestamp and action info
            $mailMessage
                ->line('Total errors: ' . $totalErrors)
                ->line('Import time: ' . now()->format('Y-m-d H:i:s'))
                ->action('View Import History', url('/admin/import-history'))
                ->line('This is an automated message. Please do not reply to this email.');
                
            // Check if we need to include a CSV attachment
            if ($totalErrors > 10) {
                // Generate a CSV tempfile
                $csvData = $this->generateCsvContent();
                $tempFilePath = tempnam(sys_get_temp_dir(), 'import_errors_');
                file_put_contents($tempFilePath, $csvData);
                
                // Attach the CSV file
                $mailMessage->attachData(
                    $csvData,
                    'import_errors_' . date('Y-m-d_His') . '.csv',
                    ['mime' => 'text/csv']
                );
            }
            
            return $mailMessage;
        } catch (\Exception $e) {
            Log::error('Failed to build notification email', [
                'error' => $e->getMessage() 
            ]);
            
            // Fallback to a simple message
            return (new MailMessage)
                ->subject('Import Error Report')
                ->line('There were errors during the email contacts import.')
                ->line('Please check logs for details.');
        }
    }
    
    /**
     * Generate CSV content for all errors
     *
     * @return string
     */
    protected function generateCsvContent(): string
    {
        $csv = "Error Type,Row,Email,Reason\n";
        
        foreach ($this->errorData['existing'] ?? [] as $error) {
            $csv .= 'Existing,' . $error['row'] . ',"' . $error['email'] . '","' . ($error['reason'] ?? 'Already exists') . "\"\n";
        }
        
        foreach ($this->errorData['invalid'] ?? [] as $error) {
            $csv .= 'Invalid,' . $error['row'] . ',"' . $error['email'] . '","' . ($error['reason'] ?? 'Invalid format') . "\"\n";
        }
        
        foreach ($this->errorData['empty'] ?? [] as $error) {
            $csv .= 'Empty,' . $error['row'] . ',"' . $error['email'] . '","' . ($error['reason'] ?? 'Missing required fields') . "\"\n";
        }
        
        return $csv;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'existing_count' => count($this->errorData['existing'] ?? []),
            'invalid_count' => count($this->errorData['invalid'] ?? []),
            'empty_count' => count($this->errorData['empty'] ?? []),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}