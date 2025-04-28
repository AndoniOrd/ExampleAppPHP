<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Markdown;
use Illuminate\Support\HtmlString;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Template Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('subject_line')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->nullable()
                        ->maxLength(500),
                    Forms\Components\Hidden::make('creator')
                        ->default(auth()->id())
                        ->dehydrated(true),
                    Forms\Components\Hidden::make('creation_date')
                        ->default(now()->format('Y-m-d'))
                        ->dehydrated(true),
                    Forms\Components\Hidden::make('last_updated_date')
                        ->default(now()->format('Y-m-d'))
                        ->dehydrated(true),
                    Forms\Components\Hidden::make('category')
                        ->default('marketing')
                        ->dehydrated(true),
                    Forms\Components\Hidden::make('status')
                        ->default('active')
                        ->dehydrated(true),
                    Forms\Components\Hidden::make('preview_content')
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('from_name')
                        ->required()
                        ->label('From Name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('from_address')
                        ->required()
                        ->label('From Email')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\Tabs::make('Content')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('Visual Editor')
                                ->schema([
                                    Forms\Components\RichEditor::make('html_content')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                                            $set('preview_content', $state);
                                            $set('plain_text_version', strip_tags($state));
                                        })
                                        ->columnSpanFull(),
                                ]),
                            Forms\Components\Tabs\Tab::make('HTML Code Editor')
                                ->schema([
                                    Forms\Components\Textarea::make('html_code_editor')
                                        ->label('')
                                        ->default(fn(Forms\Get $get) => $get('html_content') ?? '')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                                            $set('html_content', $state);
                                            $set('preview_content', $state);
                                            $set('plain_text_version', strip_tags($state));
                                        })
                                        ->extraInputAttributes([
                                            'style' => 'font-family: monospace;',
                                            'spellcheck' => 'false'
                                        ])
                                        ->rows(20)
                                        ->columnSpanFull(),
                                ]),
                            Forms\Components\Tabs\Tab::make('Plain Text')
                                ->schema([
                                    Forms\Components\Textarea::make('plain_text_version')
                                        ->required()
                                        ->rows(10)
                                        ->columnSpanFull(),
                                ]),
                            Forms\Components\Tabs\Tab::make('Preview')
                                ->schema([
                                    Forms\Components\Placeholder::make('preview')
                                        ->content(function (Forms\Get $get) {
                                            return new HtmlString($get('preview_content') ?: $get('html_content') ?: 'No content to preview');
                                        })
                                        ->columnSpanFull(),
                                ]),
                            Forms\Components\Tabs\Tab::make('HTML Code')
                                ->schema([
                                    Forms\Components\Textarea::make('html_code_view')
                                        ->label('')
                                        ->readOnly()
                                        ->rows(20)
                                        ->extraInputAttributes(['style' => 'font-family: monospace;'])
                                        ->default(function (Forms\Get $get) {
                                            return htmlspecialchars($get('html_content') ?? '');
                                        })
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->activeTab(1)
                        ->columnSpanFull(),
                ]),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
            Tables\Columns\TextColumn::make('subject_line') // Changed from 'subject' to 'subject_line'
                ->label('Subject')
                ->limit(50)
                ->searchable(),
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
          
            Tables\Actions\Action::make('preview')
            ->label('Preview')
            ->icon('heroicon-o-eye')
            ->modalHeading(fn($record) => $record->name . ' - Preview')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalWidth('max-w-5xl') // Make the modal wider
            ->modalContent(fn($record) => view('filament.resources.email-template-preview', [
                'emailTemplate' => $record,
                'showCode' => false
            ])),
        Tables\Actions\Action::make('view_code')
            ->label('View Code')
            ->icon('heroicon-o-code-bracket')
            ->modalHeading(fn($record) => $record->name . ' - HTML Code')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalWidth('max-w-5xl') // Make the modal wider
            ->modalContent(fn($record) => view('filament.resources.email-template-preview', [
                'emailTemplate' => $record,
                'showCode' => true
            ])),
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Infolists\Components\Section::make('Template Details')
                ->schema([
                    Infolists\Components\TextEntry::make('name'),
                    Infolists\Components\TextEntry::make('subject_line')
                        ->label('Subject'),
                    Infolists\Components\Tabs::make('Content')
                        ->tabs([
                            Infolists\Components\Tabs\Tab::make('Preview')
                                ->schema([
                                    Infolists\Components\TextEntry::make('html_content')
                                        ->formatStateUsing(fn ($state) => new HtmlString($state))
                                        ->columnSpanFull(),
                                ]),
                            Infolists\Components\Tabs\Tab::make('HTML Code')
                                ->schema([
                                    Infolists\Components\TextEntry::make('html_content')
                                        ->formatStateUsing(fn ($state) => htmlspecialchars($state))
                                        ->html()
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->columnSpanFull(),
                ]),
                
            Infolists\Components\Section::make('Usage')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('campaigns')
                        ->schema([
                            Infolists\Components\TextEntry::make('name')
                                ->url(fn ($record) => 
                                    CampaignPlanningResource::getUrl('edit', ['record' => $record])),
                            Infolists\Components\TextEntry::make('status_type')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'draft' => 'gray',
                                    'scheduled' => 'warning',
                                    'processing' => 'info',
                                    'completed' => 'success',
                                    default => 'gray',
                                }),
                            Infolists\Components\TextEntry::make('scheduled_time')
                                ->dateTime(),
                        ])
                        ->columns(3),
                ])
                ->collapsible(),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'view' => Pages\ViewEmailTemplate::route('/{record}'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
            'preview' => Pages\PreviewEmailTemplate::route('/{record}/preview'),
        ];
    }
}