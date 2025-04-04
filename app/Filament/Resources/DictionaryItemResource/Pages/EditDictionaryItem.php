<?php

namespace App\Filament\Resources\DictionaryItemResource\Pages;

use App\Filament\Resources\DictionaryItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDictionaryItem extends EditRecord
{
    protected static string $resource = DictionaryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
