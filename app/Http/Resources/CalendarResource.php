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

<<<<<<< HEAD:app/Http/Resources/CalendarResource.php
class CalendarResource extends JsonResource
=======
class CampaignPlanningResource extends Resource
>>>>>>> borja_api:app/Http/Resources/CampaignPlanningResource.php
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
                                Forms\Components\TextInput::make('subject_line')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('html_content')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('plain_text_version')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Hidden::make('creator')
                                    ->default(auth()->id()),
                                Forms\Components\Hidden::make('creation_date')
                                    ->default(now()->format('Y-m-d')),
                                Forms\Components\Hidden::make('last_updated_date')
                                    ->default(now()->format('Y-m-d')),
                                Forms\Components\Hidden::make('category')
                                    ->default('marketing'),
                                Forms\Components\Hidden::make('status')
                                    ->default('active'),
                            ])
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('view_template')
                                    ->icon('heroicon-m-eye')
                                    ->label('Preview')
                                    ->url(function (Forms\Get $get) {
                                        $templateId = $get('email_template_id');
                                        if (!$templateId) {
                                            return null;
                                        }
                                        return route('filament.admin.resources.email-templates.preview', ['record' => $templateId]);
                                    })
                                    ->openUrlInNewTab()
                                    ->visible(fn (Forms\Get $get) => $get('email_template_id') !== null)
                            ),
                        Forms\Components\Select::make('mailing_list_id')
                            ->relationship('mailingList', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
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
                    ])->columns(2),
            ]);
    }

    // Rest of the class remains the same
}