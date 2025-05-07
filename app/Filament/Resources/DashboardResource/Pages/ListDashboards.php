<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DashboardResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDashboards extends ListRecords
{
    protected static string $resource = DashboardResource::class; // Initialize the resource property
    
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?int $navigationSort = 1;

    // Hide the table since we're using widgets
    protected function getTableQuery(): Builder|null
    {
        return static::$resource::getModel()::whereRaw('1 = 0');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\DashboardResource\Widgets\StatsOverview::class,
            \App\Filament\Resources\DashboardResource\Widgets\CampaignChart::class,
            \App\Filament\Widgets\CampaignProgress::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Resources\DashboardResource\Widgets\UpcomingEvents::class,
            \App\Filament\Resources\DashboardResource\Widgets\LatestContacts::class,
        ];
    }
}