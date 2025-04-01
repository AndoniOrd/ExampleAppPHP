<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DictionaryItemResource\Pages;
use App\Filament\Resources\DictionaryItemResource\RelationManagers;
use App\Models\DictionaryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DictionaryItemResource extends Resource
{
    protected static ?string $model = DictionaryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('dictionary_id')
                ->relationship('dictionary', 'name')
                ->required(),
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\TextInput::make('value')
                ->required()
                ->maxLength(2),
            Forms\Components\Textarea::make('description'),
            Forms\Components\TextInput::make('order')
                ->numeric()
                ->default(0),
        ]);
}

public static function table(Table $table): Table
{
    return $table
    ->columns([
        Tables\Columns\TextColumn::make('dictionary.name')
            ->label('Dictionary')
            ->sortable(),
        Tables\Columns\TextColumn::make('name')
            ->searchable()
            ->sortable(),
        Tables\Columns\TextColumn::make('value')
            ->searchable(),
        Tables\Columns\TextColumn::make('value')
            ->badge()
            ->color(function (DictionaryItem $record) {
                if ($record->dictionary->name === 'STATUS') {
                    return match($record->value) {
                        'subscribed' => 'success',
                        'unsubscribed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray'
                    };
                }
                return 'gray';
            }),
        Tables\Columns\TextColumn::make('description')
            ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('order')
                ->numeric()
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
            'index' => Pages\ListDictionaryItems::route('/'),
            'create' => Pages\CreateDictionaryItem::route('/create'),
            'edit' => Pages\EditDictionaryItem::route('/{record}/edit'),
        ];
    }
}
