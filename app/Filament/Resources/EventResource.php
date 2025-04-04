<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Filament\Resources\EventResource\Pages\ListEvents;
use App\Filament\Resources\EventResource\Pages\ViewEvent;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\CampaignPlanning;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $slug = 'events';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Eventos';
    protected static ?string $navigationGroup = 'Gerenciamento de Eventos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Campaign name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Starting date')
                            ->required(),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Finishing date')
                            ->required(),
                    ]),
                // Fixed: Added full namespace for Select component
                Forms\Components\Select::make('campaign_planning_id')
                    ->label('Campaign Planning')
                    ->options(CampaignPlanning::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Select a campaign planning'),

                Toggle::make('status_type')
                    ->label('Active Status')
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false)
                    ->default(true)
                    ->formatStateUsing(fn($state) => $state === 'active')
                    ->dehydrateStateUsing(fn($state) => $state ? 'active' : 'inactive'),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Campaign name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Starting Date')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Finishing Date')
                    ->date()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'view' => ViewEvent::route('/{record}'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}