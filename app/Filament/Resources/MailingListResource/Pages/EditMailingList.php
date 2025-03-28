<?php

namespace App\Filament\Resources\MailingListResource\Pages;

use App\Filament\Resources\MailingListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMailingList extends EditRecord
{
    protected static string $resource = MailingListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
