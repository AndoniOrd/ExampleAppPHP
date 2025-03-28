<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailContactResource\Pages;
use App\Filament\Resources\EmailContactResource\RelationManagers;
use App\Models\EmailContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailContactResource extends Resource
{
    protected static ?string $model = EmailContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('mailingLists')
                    ->relationship('mailingLists', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
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
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                // Changed from mailingList.name to mailingLists.name
                Tables\Columns\TextColumn::make('mailingLists.name')
                    ->badge()  // Optional: shows items as badges
                    ->separator(',')  // Separate multiple entries with commas
                    ->label('Mailing Lists')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailContacts::route('/'),
            'create' => Pages\CreateEmailContact::route('/create'),
            'edit' => Pages\EditEmailContact::route('/{record}/edit'),
        ];
    }
}