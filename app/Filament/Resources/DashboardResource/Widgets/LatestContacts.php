<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Filament\Resources\EmailContactResource;
use App\Models\EmailContact;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class LatestContacts extends BaseWidget
{
    protected static ?string $heading = 'Recently Added Contacts';
    
    protected int | string | array $columnSpan = 'full';
    
    /**
     * Define the query used for the table.
     *
     * @return Builder|Relation|null
     */
    protected function getTableQuery(): Builder|Relation|null
    {
        return EmailContact::query()
            ->latest('created_at')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('First Name')
                ->searchable()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('last_name')
                ->label('Last Name')
                ->searchable()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->sortable(),
                
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'success' => fn($state): bool => strtolower($state) === 'active',
                    'danger' => fn($state): bool => strtolower($state) === 'inactive',
                    'warning' => fn($state): bool => strtolower($state) === 'pending',
                ]),
                
            Tables\Columns\TextColumn::make('mailingLists.name')
                ->label('Mailing Lists')
                ->badge()
                ->separator(','),
                
            Tables\Columns\TextColumn::make('created_at')
                ->label('Added')
                ->dateTime()
                ->sortable(),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('edit')
                ->url(fn (EmailContact $record): string => EmailContactResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
