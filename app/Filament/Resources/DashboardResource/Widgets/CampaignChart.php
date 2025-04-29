<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\CampaignPlanning;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CampaignChart extends ChartWidget
{
    protected static ?string $heading = 'Campaign Performance (Last 30 Days)';
    
    protected static ?string $pollingInterval = null;
    
    protected static ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'month';

    protected function getData(): array
    {
        $activeData = $this->getCampaignData('scheduled', 'processing');
        $completedData = $this->getCampaignData('completed');
        
        return [
            'datasets' => [
                [
                    'label' => 'Active Campaigns',
                    'data' => $activeData['values'],
                    'backgroundColor' => '#ffc107',
                    'borderColor' => '#ffc107',
                ],
                [
                    'label' => 'Completed Campaigns',
                    'data' => $completedData['values'],
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
            ],
            'labels' => $activeData['labels'],
        ];
    }
    
    protected function getCampaignData(string ...$statuses): array
    {
        $query = CampaignPlanning::query()
            ->whereIn('status_type', $statuses);
        
        $data = match ($this->filter) {
            'week' => Trend::query($query)
                ->between(
                    start: now()->startOfWeek(),
                    end: now()->endOfWeek(),
                )
                ->perDay()
                ->count(),
            'month' => Trend::query($query)
                ->between(
                    start: now()->startOfMonth()->subDays(30),
                    end: now()->endOfMonth(),
                )
                ->perDay()
                ->count(),
            'year' => Trend::query($query)
                ->between(
                    start: now()->startOfYear(),
                    end: now()->endOfYear(),
                )
                ->perMonth()
                ->count(),
        };
        
        return [
            'labels' => $data->map(fn (TrendValue $value) => $value->date)->toArray(),
            'values' => $data->map(fn (TrendValue $value) => $value->aggregate),
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            'year' => 'This Year',
        ];
    }
}