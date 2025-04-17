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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('creation_date')
                    ->default(now())
                    ->required(),
                // Add other required fields from your database table
                Forms\Components\TextInput::make('description')
                    ->required(),
                Forms\Components\Select::make('created_by')
                    ->relationship('creator', 'name') // Assuming you have a User model
                    ->required(),
                Forms\Components\Toggle::make('is_public')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required(),
                    Forms\Components\DateTimePicker::make('last_updated_date')
    ->default(now())
    ->required(),

    Forms\Components\Select::make('owner_id')
    ->label('Owner')
    ->relationship('owner', 'name') // Assumes a relation like: $this->belongsTo(User::class, 'owner_id')
    ->required(),

            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creation_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('emailContacts_count')
                    ->counts('emailContacts')
                    ->label('Contacts'),
            ])
            ->filters([
                // Filters can be added here
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