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
        // Assign current user as owner if not explicitly set
        $data['owner_id'] = $data['owner_id'] ?? auth()->id();
        
        // Ensure required fields have default values if not provided
        $data['status'] = $data['status'] ?? 'active';
        $data['description'] = $data['description'] ?? '';
        $data['last_updated_date'] = $data['last_updated_date'] ?? now();
        $data['creation_date'] = $data['creation_date'] ?? now();
        
        return $data;
    }
}