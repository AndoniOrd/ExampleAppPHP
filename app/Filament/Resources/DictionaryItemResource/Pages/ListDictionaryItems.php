<?php

namespace App\Filament\Resources\DictionaryItemResource\Pages;

use App\Filament\Resources\DictionaryItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDictionaryItems extends ListRecords
{
    protected static string $resource = DictionaryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
