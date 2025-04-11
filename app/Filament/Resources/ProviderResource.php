<?php

namespace App\Filament\Resources;

use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('active')
                            ->required()
                            ->default(true),
                    ]),

                    Forms\Components\TextInput::make('from_address')
                    ->required()
                    ->email()
                    ->label('From Address'),
                Forms\Components\TextInput::make('from_name')
                    ->required()
                    ->label('From Name'),
                
                Forms\Components\Section::make('SMTP Configuration')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('smtp_host')
                            ->required()
                            ->label('SMTP Host')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('smtp_port')
                            ->required()
                            ->label('SMTP Port')
                            ->numeric()
                            ->default(465),
                        Forms\Components\Select::make('smtp_encryption')
                            ->label('SMTP Encryption')
                            ->options([
                                'ssl' => 'SSL',
                                'tls' => 'TLS',
                                null => 'None',
                            ]),
                        Forms\Components\TextInput::make('smtp_username')
                            ->required()
                            ->label('SMTP Username'),
                        Forms\Components\TextInput::make('smtp_password')
                            ->required()
                            ->label('SMTP Password')
                            ->password(),
                    ]),
                
                Forms\Components\Section::make('IMAP Configuration')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('imap_host')
                            ->required()
                            ->label('IMAP Host')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('imap_port')
                            ->required()
                            ->label('IMAP Port')
                            ->numeric()
                            ->default(993),
                        Forms\Components\Select::make('imap_encryption')
                            ->label('IMAP Encryption')
                            ->options([
                                'ssl' => 'SSL',
                                'tls' => 'TLS',
                                null => 'None',
                            ]),
                        Forms\Components\TextInput::make('imap_username')
                            ->required()
                            ->label('IMAP Username'),
                        Forms\Components\TextInput::make('imap_password')
                            ->required()
                            ->label('IMAP Password')
                            ->password(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('smtp_host')
                    ->label('SMTP Host'),
                Tables\Columns\TextColumn::make('imap_host')
                    ->label('IMAP Host'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ProviderResource\Pages\ListProviders::route('/'),
            'create' => \App\Filament\Resources\ProviderResource\Pages\CreateProvider::route('/create'),
            'view' => \App\Filament\Resources\ProviderResource\Pages\ViewProvider::route('/{record}'),
            'edit' => \App\Filament\Resources\ProviderResource\Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}