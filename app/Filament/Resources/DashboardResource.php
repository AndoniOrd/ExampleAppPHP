<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DashboardResource\Pages;
use App\Models\CampaignPlanning;
use App\Filament\Pages\ListDashboards;
use App\Models\EmailContact;
use App\Models\MailingList;
use App\Models\Event;
use App\Models\EmailTemplate;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;

class DashboardResource extends Resource
{
    protected static ?string $model = CampaignPlanning::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?int $navigationSort = 1;
    
    // Make dashboard show up before all other resources
    protected static ?string $slug = 'dashboard';

    public static function getNavigationGroup(): ?string
    {
        return null; // No group for dashboard (top level)
    }

    public static function canCreate(): bool
    {
        return false; // Disable creation from dashboard
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Empty form as this is just for display
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Active Campaigns')
            ->description('Currently scheduled or processing campaigns')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (CampaignPlanning $record): string => $record->description ?? 'No description'),
                
                Tables\Columns\TextColumn::make('emailTemplate.name')
                    ->label('Template')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('mailingList.name')
                    ->label('Mailing List')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('contacts_count')
                    ->label('Recipients')
                    ->state(function (CampaignPlanning $record): int {
                        // Only return a count if the mailing list exists
                        if ($record->mailingList) {
                            return $record->mailingList->emailContacts()->count();
                        }
                        return 0;
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('scheduled_time')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('scheduled_time', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status_type')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'processing' => 'Processing',
                    ])
                    ->default(['scheduled', 'processing'])
                    ->multiple(),
                    
                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming (Next 7 Days)')
                    ->query(function ($query) {
                        return $query->where('scheduled_time', '>=', now())
                            ->where('scheduled_time', '<=', now()->addDays(7));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->deferLoading();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Pages\ListDashboards::route('/'),
        ];
    }
}