<?php

namespace App\Filament\Resources\EmailContactResource\Pages;

use App\Filament\Resources\EmailContactResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\FileUpload;

class ListEmailContacts extends ListRecords
{
    protected static string $resource = EmailContactResource::class;

    protected function getTableActions(): array
    {
        return [
            Action::make('import')
                ->label('Import Contacts')
                ->form([
                    FileUpload::make('file')
                        ->label('CSV File')
                        ->acceptedFileTypes(['text/csv', 'text/plain'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    if ($data['file']) {
                        $path = $data['file']->getRealPath();
                        if (($handle = fopen($path, 'r')) !== false) {
                            while (($row = fgetcsv($handle)) !== false) {
                                // Adjust the parsing based on your CSV structure.
                                // For example, assume the CSV has columns: email,name,last_name,...
                                \App\Models\EmailContact::create([
                                    'email'     => $row[0],
                                    'name'      => $row[1] ?? null,
                                    'last_name' => $row[2] ?? null,
                                    // add other fields as needed
                                ]);
                            }
                            fclose($handle);
                            $this->notify('success', 'Contacts imported successfully!');
                        } else {
                            $this->notify('danger', 'Unable to open the file.');
                        }
                    }
                }),
        ];
    }
}
