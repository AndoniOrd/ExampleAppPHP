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
    $data['owner_id'] = auth()->id(); // Assign current user
    return $data;
}

}
