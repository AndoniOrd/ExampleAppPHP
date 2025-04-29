<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class UpcomingEvents extends BaseWidget
{
    protected static ?string $heading = 'Upcoming Events';
    
    protected int | string | array $columnSpan = 'full';
    
    /**
     * Define the query used for the table.
     *
     * @return Builder|Relation|null
     */
    protected function getTableQuery(): Builder|Relation|null
    {
        return Event::query()
            ->where('starts_at', '>=', now())
            ->where('status_type', 'active')
            ->orderBy('starts_at');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Event Name')
                ->searchable(),
                
            Tables\Columns\TextColumn::make('campaignPlanning.name')
                ->label('Linked Campaign')
                ->searchable(),
                
            Tables\Columns\TextColumn::make('starts_at')
                ->label('Date')
                ->date()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('time_to_event')
                ->label('Starts In')
                ->formatStateUsing(function ($record) {
                    $now = now();
                    $start = $record->starts_at;
                    
                    if ($start->isPast()) {
                        return 'In progress';
                    }
                    
                    $diff = $now->diff($start);
                    
                    if ($diff->days > 0) {
                        return $diff->days . ' days';
                    }
                    
                    if ($diff->h > 0) {
                        return $diff->h . ' hours';
                    }
                    
                    return $diff->i . ' minutes';
                }),
                
            Tables\Columns\BadgeColumn::make('status_type')
                ->label('Status')
                ->colors([
                    'success' => fn($state): bool => strtolower($state) === 'active',
                    'danger' => fn($state): bool => strtolower($state) !== 'active',
                ]),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->url(fn (Event $record): string => EventResource::getUrl('view', ['record' => $record])),
                
            Tables\Actions\Action::make('edit')
                ->url(fn (Event $record): string => EventResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
