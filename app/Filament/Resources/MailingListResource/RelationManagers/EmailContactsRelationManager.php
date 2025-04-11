<?php

namespace App\Filament\Resources\MailingListResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmailContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'emailContacts';

    public function form(Form $form): Form
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'subscribed' => 'success',
                        'unsubscribed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                // Filters can be added here
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'subscribed' => 'Subscribed',
                                'pending' => 'Pending',
                            ])
                            ->default('subscribed')
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}