<?php

namespace App\Filament\Resources\CampaignPlanningResource\Pages;

use App\Filament\Resources\CampaignPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaignPlanning extends EditRecord
{
    protected static string $resource = CampaignPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

