<?php

namespace App\Filament\Resources\MailingListResource\RelationManagers;

use App\Models\DictionaryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\SelectColumn;

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
                Forms\Components\Select::make('status')
                    ->options(
                        DictionaryItem::whereHas('dictionary', fn($q) => $q->where('name', 'STATUS'))
                            ->pluck('name', 'value')
                    )
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        // Get status options
        $statusOptions = DictionaryItem::whereHas('dictionary', fn($q) => $q->where('name', 'STATUS'))
            ->pluck('name', 'value')
            ->toArray();

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                
                // Use a custom column for status
                Tables\Columns\TextColumn::make('status')
                    ->getStateUsing(function ($record) {
                        $mailingListId = $this->getOwnerRecord()->id;
                        $pivotRecord = $record->mailingLists()->where('mailing_list_id', $mailingListId)->first();
                        return $pivotRecord ? $pivotRecord->pivot->status : 'N/A';
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'subscribed' => 'success',
                        'unsubscribed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray'
                    })
                    ->label('Status'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options($statusOptions)
            ])
            ->actions([
                Tables\Actions\Action::make('changeStatus')
                    ->label('Change Status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options($statusOptions)
                            ->required()
                    ])
                    ->action(function ($record, $data) {
                        // Get the current mailing list
                        $mailingListId = $this->getOwnerRecord()->id;
                        
                        // Update the pivot table
                        $record->mailingLists()->updateExistingPivot($mailingListId, [
                            'status' => $data['status']
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}