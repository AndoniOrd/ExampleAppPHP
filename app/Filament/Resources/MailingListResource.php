<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailingListResource\Pages;
use App\Filament\Resources\MailingListResource\RelationManagers;
use App\Models\MailingList;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MailingListResource extends Resource
{
    protected static ?string $model = MailingList::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // First section - Basic information (most important fields)
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->options([
                                'newsletter' => 'Newsletter',
                                'promotions' => 'Promotions',
                                'updates' => 'Updates',
                            ])
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'draft' => 'Draft',
                                'archived' => 'Archived',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(3),

                // Second section - Additional details
                Forms\Components\Section::make('Additional Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->nullable(),

                        Forms\Components\TextInput::make('tags')
                            ->nullable()
                            ->helperText('Comma-separated list of tags (e.g. "summer,sale,2025")'),

                        Forms\Components\DateTimePicker::make('creation_date')
                            ->default(now())
                            ->seconds(false)
                            ->displayFormat('M j, Y H:i')
                            ->native(false)
                            ->disabled() // Make it read-only since it's set automatically
                            ->dehydrated() // Ensure the value is still saved
                            ->required(),

                        Forms\Components\DateTimePicker::make('last_updated_date')
                            ->default(now())
                            ->seconds(false)
                            ->displayFormat('M j, Y H:i')
                            ->native(false)
                            ->required(),
                    ])->columns(2)->collapsed(),

                // Third section - Ownership Information
                Forms\Components\Section::make('Ownership')
                    ->schema([
                        Forms\Components\Select::make('owner_id')
                            ->label('Owner')
                            ->relationship('owner', 'name')
                            ->default(auth()->id())
                            ->required(),

                        Forms\Components\Select::make('created_by')
                            ->label('Created By')
                            ->relationship('creator', 'name')
                            ->default(auth()->id())
                            ->required(),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'newsletter' => 'Newsletter',
                        'promotions' => 'Promotions',
                        'updates'    => 'Updates',
                        default      => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('creation_date')
                    ->dateTime('M j, Y H:i') // Show time in table
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'draft'    => 'warning',
                        'archived' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('emailContacts_count')
                    ->counts('emailContacts')
                    ->label('Contacts'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EmailContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMailingLists::route('/'),
            'create' => Pages\CreateMailingList::route('/create'),
            'edit' => Pages\EditMailingList::route('/{record}/edit'),
        ];
    }
}