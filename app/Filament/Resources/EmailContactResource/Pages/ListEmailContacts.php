<?php

namespace App\Filament\Resources\EmailContactResource\Pages;

use App\Filament\Resources\EmailContactResource;
use App\Imports\EmailContactsImport;
use App\Mail\ImportErrorsNotification;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
                        ->storeFiles(false), // Add this line
                    Select::make('mailingLists')
                        ->label('Add to Mailing Lists')
                        ->options(\App\Models\MailingList::pluck('name', 'id'))
                        ->multiple()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $import = new EmailContactsImport($data['mailingLists'] ?? []);

                    try {
                        // Get the temporary uploaded file path
                        $filePath = $data['file']->getRealPath();
                        
                        Excel::import($import, $filePath);

                        $successCount = $import->getSuccessCount();
                        $errorCount = $import->getTotalErrors();

                        Notification::make()
                            ->title('Import Completed')
                            ->body("Successfully imported {$successCount} contacts" . 
                                  ($errorCount > 0 ? " with {$errorCount} errors" : ''))
                            ->success()
                            ->send();

                        if ($errorCount > 0) {
                            Mail::to(config('mail.admin_email'))
                                ->send(new ImportErrorsNotification([
                                    'existing' => $import->getExistingEmails(),
                                    'invalid' => $import->getInvalidEmails(),
                                    'empty' => $import->getEmptyData(),
                                ]));
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
        ];
    }
}