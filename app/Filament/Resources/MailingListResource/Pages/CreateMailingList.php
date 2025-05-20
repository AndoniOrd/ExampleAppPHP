<?php

namespace App\Filament\Resources\MailingListResource\Pages;

use App\Filament\Resources\MailingListResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMailingList extends CreateRecord
{
    protected static string $resource = MailingListResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id'] = $data['owner_id'] ?? auth()->id();
        $data['created_by'] = auth()->id();
        $data['status'] = $data['status'] ?? 'active';
        $data['type'] = $data['type'] ?? 'newsletter'; // Ensure type field has a default
        $data['description'] = $data['description'] ?? '';
        $data['last_updated_date'] = now();
        $data['creation_date'] = now();
        
        return $data;
    }
}