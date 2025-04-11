<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Filament\Resources\EventResource\Pages\ListEvents;
use App\Filament\Resources\EventResource\Pages\ViewEvent;
use App\Models\Event;
use App\Models\CampaignPlanning;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $slug = 'events';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Events';
    protected static ?string $navigationGroup = 'Event administration';

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
                            ->required()
                            ->reactive() // Para reaccionar a cambios
                            ->afterStateUpdated(function (callable $set, $state) {
                                // Actualizamos ends_at para que sea igual a starts_at
                                $set('ends_at', $state);
                            }),
                        // Campo ends_at: se muestra pero deshabilitado; se fuerza su deshidratación para que se incluya en el envío
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Finishing date')
                            ->disabled()
                            ->dehydrated(true)
                            ->required()
                            ->default(fn() => now()),
                    ]),
                Forms\Components\Select::make('campaign_planning_id')
                    ->label('Campaign Planning')
                    ->options(CampaignPlanning::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Select a campaign planning'),
                Forms\Components\Toggle::make('status_type')
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
                Tables\Columns\BadgeColumn::make('status_type')
                    ->label('Status')
                    ->colors([
                        'success' => fn($state): bool => strtolower($state) === 'active',
                        'danger' => fn($state): bool => strtolower($state) !== 'active',
                    ])
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
