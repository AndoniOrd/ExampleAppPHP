<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmailTemplate extends CreateRecord
{
    protected static string $resource = EmailTemplateResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure all required fields are present
        $data['creator'] = auth()->id() ?? 1;
        $data['creation_date'] = now()->format('Y-m-d');
        $data['last_updated_date'] = now()->format('Y-m-d');
        $data['category'] = $data['category'] ?? 'marketing';
        $data['status'] = $data['status'] ?? 'active';
        
        // Ensure plain_text_version is populated if missing
        if (empty($data['plain_text_version']) && !empty($data['html_content'])) {
            $data['plain_text_version'] = strip_tags($data['html_content']);
        }
        
        return $data;
    }
    
    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);
        
        // Handle any post-creation logic if needed
        
        return $record;
    }
}