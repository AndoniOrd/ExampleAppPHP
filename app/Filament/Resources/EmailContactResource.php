<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailContactResource\Pages;
use App\Models\EmailContact;
use App\Models\MailingList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailContactResource extends Resource
{
    protected static ?string $model = EmailContact::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending'
                    ])
                    ->required(),
                Forms\Components\Select::make('source')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                        'manual' => 'Manual',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('has_crm')
                    ->label('Do you have CRM?')
                    ->required(),
                Forms\Components\DatePicker::make('opt_in_date')
                    ->required(),
                Forms\Components\Toggle::make('opt_in_confirmation')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->maxLength(1000)
                    ->rows(5)
                    ->placeholder('Write any additional notes here...'),
                Forms\Components\Select::make('mailingLists')
                    ->relationship('mailingLists', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('mailingLists.name')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc') // Updated key
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
            // Relation managers can be added here
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
