<?php

namespace App\Filament\Resources\CalendarResource\Widgets;

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
use Filament\Forms\Components\Toggle;

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
            'selectable' => true,
            'selectMirror' => true,
            // Bloqueamos la selección de rangos: solo se permite un único punto de tiempo
            'selectAllow' => <<<JS
                function(selectInfo) {
                    // Si la diferencia en minutos es 0 (o menor) se entiende que se seleccionó un solo instante.
                    return moment(selectInfo.end).diff(moment(selectInfo.start), 'minutes') <= 0;
                }
            JS,
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
                    ->id($event->id)
                    ->title($event->name)
                    ->start($event->starts_at)
                    ->end($event->ends_at)
                    ->url(
                        url: EventResource::getUrl('view', ['record' => $event]),
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
                    // Se fuerza que ends_at sea igual a starts_at, por lo que se deshabilita su edición.
                    Forms\Components\Hidden::make('ends_at')
                        ->dehydrated(true)       
                        ->default(fn() => now()),  
                ]),

            Toggle::make('status_type')
                ->label('Active Status')
                ->onColor('success')
                ->offColor('danger')
                ->inline(false)
                ->default(true)
                ->formatStateUsing(fn($state) => $state === 'active')
                ->dehydrateStateUsing(fn($state) => $state ? 'active' : 'inactive'),
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(function (Forms\Form $form, array $arguments) {
                    $start = $arguments['start'] ?? now();
                    $form->fill([
                        'starts_at' => $start,
                        // Forzamos que la fecha de fin sea la misma que la de inicio
                        'ends_at' => $start,
                        // Al crear se establece el evento como activo por defecto
                        'status_type' => true,
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
                        // Al editar, se utiliza la fecha de inicio del evento
                        'starts_at' => $arguments['event']['start'] ?? $record->starts_at,
                        'ends_at' => $arguments['event']['start'] ?? $record->starts_at,
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