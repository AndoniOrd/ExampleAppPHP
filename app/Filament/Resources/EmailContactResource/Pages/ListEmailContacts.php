<?php

namespace App\Filament\Resources\EmailContactResource\Pages;

use App\Filament\Resources\EmailContactResource;
use App\Imports\EmailContactsImport;
use App\Services\EmailNotificationService;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ListEmailContacts extends ListRecords
{
    protected static string $resource = EmailContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('Import Contacts')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('file')
                        ->label('Contacts File (CSV/Excel)')
                        ->acceptedFileTypes([
                            'text/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ])
                        ->required()
                        ->maxSize(10240)
                        ->storeFiles(false),
                    Select::make('mailingLists')
                        ->label('Add to Mailing Lists')
                        ->options(\App\Models\MailingList::pluck('name', 'id'))
                        ->multiple()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $import = new EmailContactsImport($data['mailingLists'] ?? []);

                    try {
                        // Process the import
                        $filePath = $data['file']->getRealPath();
                        Excel::import($import, $filePath);

                        // Get statistics
                        $successCount = $import->getSuccessCount();
                        $existingCount = count($import->getExistingEmails());
                        $invalidCount = count($import->getInvalidEmails());
                        $emptyCount = count($import->getEmptyData());
                        $totalErrors = $existingCount + $invalidCount + $emptyCount;

                        // Show notification to the user
                        Notification::make()
                            ->title('Import Completed')
                            ->body($this->buildNotificationBody($successCount, $existingCount, $invalidCount, $emptyCount))
                            ->success()
                            ->send();

                        // Send error emails if there are errors
                        if ($totalErrors > 0) {
                            $this->sendErrorReport($import);
                        }

                    } catch (\Exception $e) {
                        Log::error('Import failed: ' . $e->getMessage(), [
                            'exception' => get_class($e),
                            'trace' => $e->getTraceAsString()
                        ]);
                        
                        Notification::make()
                            ->title('Import Failed')
                            ->body('An error occurred during import: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
        ];
    }

    protected function buildNotificationBody(int $successCount, int $existingCount, int $invalidCount, int $emptyCount): string
    {
        $body = "Successfully imported {$successCount} contacts";
        
        if ($existingCount > 0) {
            $body .= "<br>{$existingCount} emails already exist in database";
        }
        
        if ($invalidCount > 0) {
            $body .= "<br>{$invalidCount} invalid emails were found";
        }
        
        if ($emptyCount > 0) {
            $body .= "<br>{$emptyCount} rows had missing required fields";
        }

        return $body;
    }
    protected function sendErrorReport(EmailContactsImport $import): void
{
    try {
        $errorData = [
            'existing' => $import->getExistingEmails(),
            'invalid' => $import->getInvalidEmails(),
            'empty' => $import->getEmptyData(),
        ];

        $notificationService = app(EmailNotificationService::class);
        $success = $notificationService->sendImportErrorNotifications($errorData);

        if (!$success) {
            Notification::make()
                ->title('Error Report Delivery Issue')
                ->body('Administrators might not have received error notifications. 
                        Please verify email configuration.')
                ->persistent()
                ->warning()
                ->send();
        }
     } catch (\Exception $e) {
        Log::error('Critical error reporting failure', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    
        Notification::make()
            ->title('System Malfunction')
            ->body('Failed to process error reporting: ' . $e->getMessage())
            ->danger()
            ->send();
    }
    
        {
            try {
                $errorData = [
                    'existing' => $import->getExistingEmails(),
                    'invalid' => $import->getInvalidEmails(),
                    'empty' => $import->getEmptyData(),
                ];
        
                // Get the total error count
                $totalErrors = count($errorData['existing']) + 
                              count($errorData['invalid']) + 
                              count($errorData['empty']);
                              
                if ($totalErrors === 0) {
                    return; // No errors to report
                }
        
                // Log detailed information about the errors
                \Illuminate\Support\Facades\Log::info('Import completed with errors', [
                    'existing_count' => count($errorData['existing']),
                    'invalid_count' => count($errorData['invalid']),
                    'empty_count' => count($errorData['empty']),
                    'total_errors' => $totalErrors
                ]);
        
                // Get the service from the container
                $notificationService = app(EmailNotificationService::class);
                
                // Make sure to display a properly worded error message if it fails
                $success = $notificationService->sendImportErrorNotifications($errorData);
        
                if (!$success) {
                    Notification::make()
                        ->title('Error Report Status')
                        ->body('The error report may not have been delivered to administrators. 
                                The system will retry automatically. You can check the logs for more details.')
                        ->warning()
                        ->send();
                        
                    // Queue a retry job
                    \Illuminate\Support\Facades\Queue::later(
                        now()->addMinutes(5),
                        new \App\Jobs\RetryImportErrorNotificationJob($errorData)
                    );
                } else {
                    Notification::make()
                        ->title('Error Report Sent')
                        ->body('A detailed error report has been sent to administrators.')
                        ->success()
                        ->send();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Critical error during error reporting', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                Notification::make()
                    ->title('System Error')
                    ->body('Failed to process error reporting: ' . $e->getMessage())
                    ->danger()
                    ->persistent()
                    ->send();
            }
        }
        Log::error('Critical error reporting failure', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        Notification::make()
            ->title('System Malfunction')
            ->body('Failed to process error reporting: ' . $e->getMessage())
            ->danger()
            ->send();
    }
}
