<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Always update the last_updated_date
        $data['last_updated_date'] = now()->format('Y-m-d');
        
        // Ensure plain_text_version is populated if missing
        if (empty($data['plain_text_version']) && !empty($data['html_content'])) {
            $data['plain_text_version'] = strip_tags($data['html_content']);
        }
        
        return $data;
    }
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        
        // Handle any post-update logic if needed
        
        return $record;
    }
}