<?php

namespace App\Filament\Resources\CampaignPlanningResource\Pages;

use App\Filament\Resources\CampaignPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaignPlanning extends ViewRecord
{
    protected static string $resource = CampaignPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}