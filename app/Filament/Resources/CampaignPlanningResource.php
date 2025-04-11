<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignPlanningResource\Pages;
use App\Filament\Resources\CampaignPlanningResource\RelationManagers;
use App\Models\CampaignPlanning;
use App\Models\EmailTemplate;
use App\Enums\TrackingOptions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CampaignPlanningResource extends Resource
{
    protected static ?string $model = CampaignPlanning::class;

    // Retain marketing-specific navigation settings from borja_api branch.
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Campaign Details Section with email template preview functionality.
                Forms\Components\Section::make('Campaign Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('email_template_id')
                            ->label('Email Template')
                            ->relationship('emailTemplate', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('view_template')
                                    ->icon('heroicon-m-eye')
                                    ->label('Preview')
                                    ->requiresConfirmation(false)
                                    ->modalHeading('Email Template Preview')
                                    ->modalDescription('Preview of the selected email template')
                                    ->modalSubmitAction(false)
                                    ->modalCancelAction(false)
                                    ->modalWidth('xl')
                                    ->action(function () {
                                        // Modal is shown
                                    })
                                    ->visible(fn (Forms\Get $get) => $get('email_template_id') !== null)
                                    ->modalContent(function (Forms\Get $get) {
                                        $templateId = $get('email_template_id');
                                        if (!$templateId) {
                                            return 'No template selected';
                                        }
                                        
                                        $emailTemplate = EmailTemplate::find($templateId);
                                        if (!$emailTemplate) {
                                            return 'Template not found';
                                        }

                                        return view('filament.resources.email-template-preview', [
                                            'emailTemplate' => $emailTemplate,
                                            'showCode' => false,
                                        ]);
                                    })
                            ),
                        Forms\Components\Select::make('mailing_list_id')
                            ->relationship('mailingList', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                // Schedule Information Section
                Forms\Components\Section::make('Schedule Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('scheduled_time')
                            ->required(),
                        Forms\Components\Select::make('time_zone')
                            ->required()
                            ->options(\DateTimeZone::listIdentifiers())
                            ->searchable(),
                        Forms\Components\Select::make('status_type')
                            ->required()
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                            ])
                            ->default('draft'),
                        Forms\Components\Select::make('scheduled_by')
                            ->relationship('scheduledBy', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                // Sender Information Section
                Forms\Components\Section::make('Sender Information')
                    ->schema([
                        Forms\Components\TextInput::make('send_from_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('send_from_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('reply_to_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('tracking_options')
                            ->options([
                                'open' => 'Opens Only',
                                'click' => 'Clicks Only',
                                'open_click' => 'Opens and Clicks',
                                'none' => 'No Tracking',
                            ])
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('emailTemplate.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mailingList.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scheduled_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_type')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\Filter::make('scheduled_time')
                    ->form([
                        Forms\Components\DatePicker::make('scheduled_from'),
                        Forms\Components\DatePicker::make('scheduled_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scheduled_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_time', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_time', '<=', $date),
                            );
                    }),
            ])
            ->actions([
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

    public static function getRelations(): array
    {
        return [
            // Register relation managers here if needed.
        ];
    }

    public static function getPages(): array
    {
        return [
            // Traditional list view
            'index'    => Pages\ListCampaignPlannings::route('/'),
            // Additional calendar view for a different perspective
            'calendar' => Pages\CalendarPage::route('/calendar'),
            'create'   => Pages\CreateCampaignPlanning::route('/create'),
            'view'     => Pages\ViewCampaignPlanning::route('/{record}'),
            'edit'     => Pages\EditCampaignPlanning::route('/{record}/edit'),
        ];
    }
}
