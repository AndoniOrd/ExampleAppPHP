<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignPlanningResource\Pages;
use App\Models\CampaignPlanning;
use App\Models\EmailTemplate;
use App\Enums\TrackingOptions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CampaignPlanningResource extends Resource
{
    protected static ?string $model = CampaignPlanning::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),

                        // Fixed email template selection
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('email_template_id')
                                    ->label('Email Template')
                                    ->relationship('emailTemplate', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionAction(
                                        function (Forms\Components\Actions\Action $action) {
                                            return $action
                                                ->modalHeading('Create Email Template')
                                                ->modalSubmitActionLabel('Create')
                                                ->form([
                                                    Forms\Components\TextInput::make('name')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('subject_line')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('from_name')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('from_address')
                                                        ->required()
                                                        ->email()
                                                        ->maxLength(255),
                                                    Forms\Components\RichEditor::make('html_content')
                                                        ->label('Email Content')
                                                        ->required()
                                                        ->columnSpanFull(),
                                                ])
                                                ->action(function (array $data) {
                                                    // Use Filament's model creation with proper data mapping
                                                    $template = EmailTemplate::create([
                                                        'name' => $data['name'],
                                                        'subject_line' => $data['subject_line'],
                                                        'from_name' => $data['from_name'],
                                                        'from_address' => $data['from_address'],
                                                        'html_content' => $data['html_content'],
                                                        'plain_text_version' => strip_tags($data['html_content']),
                                                        'creator' => auth()->id(),
                                                        'creation_date' => now()->format('Y-m-d'),
                                                        'last_updated_date' => now()->format('Y-m-d'),
                                                        'category' => 'marketing',
                                                        'status' => 'active',
                                                    ]);

                                                    return $template->getKey();
                                                })
                                                ->after(function (Forms\Components\Select $component, $state) {
                                                    // Refresh the select options after creation
                                                    $component->getSelectComponent()->refresh();
                                                });
                                        }
                                    )
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
                                                    'showCode' => false
                                                ]);
                                            })
                                    ),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Select::make('mailing_list_id')
                            ->relationship('mailingList', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])->columns(2),

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
                        Forms\Components\DatePicker::make('creation_date')
                            ->required()
                            ->default(now())
                            ->label('Creation Date'),
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
                    })
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaignPlannings::route('/'),
            'create' => Pages\CreateCampaignPlanning::route('/create'),
            'view' => Pages\ViewCampaignPlanning::route('/{record}'),
            'edit' => Pages\EditCampaignPlanning::route('/{record}/edit'),
        ];
    }
}