<?php
// App\Filament\Resources\CampaignPlanningResource\Pages\ListCampaignPlannings.php
namespace App\Filament\Resources\CampaignPlanningResource\Pages;

use App\Filament\Resources\CampaignPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampaignPlannings extends ListRecords
{
    protected static string $resource = CampaignPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}