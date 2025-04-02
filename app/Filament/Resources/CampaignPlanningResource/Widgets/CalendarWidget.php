<?php

namespace App\Filament\Resources\CampaignPlanningResource\Widgets;

use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Filament\Forms;
use App\Models\Event;
use App\Filament\Resources\EventResource;
use Filament\Forms\Components\Select;
use App\Models\CampaignPlanning;



class CalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Event::class;

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Event $event) {
                return EventData::make()
                    ->id($event->id) // Changed from uuid to id (assuming your model uses standard IDs)
                    ->title($event->name)
                    ->start($event->starts_at)
                    ->end($event->ends_at)
                    ->url(
                        url: EventResource::getUrl(name: 'view', parameters: ['record' => $event]),
                        shouldOpenUrlInNewTab: true
                    );
            })
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Campaign name')
                ->required(),

            Select::make('campaign_planning_id')
                ->label('Campaign Planning')
                ->options(CampaignPlanning::all()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->placeholder('Select a campaign planning'),

            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Starting date')
                        ->required(),
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('Finishing date')
                        ->required(),
                ]),
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(function (Forms\Form $form, array $arguments) {
                    $form->fill([
                        'starts_at' => $arguments['start'] ?? now(),
                        'ends_at' => $arguments['end'] ?? now()->addHour(),
                    ]);
                }),
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(function (Event $record, Forms\Form $form, array $arguments) {
                    $form->fill([
                        'name' => $record->name,
                        'starts_at' => $arguments['event']['start'] ?? $record->starts_at,
                        'ends_at' => $arguments['event']['end'] ?? $record->ends_at,
                    ]);
                }),
            DeleteAction::make(),
        ];
    }

    protected function viewAction(): ViewAction
    {
        return ViewAction::make();
    }

    public function eventDidMount(): string
    {
        return <<<JS
            function({ event, el }) {
                el.setAttribute("x-tooltip", "tooltip");
                el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
            }
        JS;
    }
}