<?php

// app/Filament/Resources/CalendarResource/Pages/CalendarPage.php

namespace App\Filament\Resources\CalendarResource\Pages;

use Filament\Pages\Page;
use Filament\Widgets\Widget;
use Filament\Resources\Pages\Page as BasePage;
use App\Filament\Resources\CalendarResource;
use App\Filament\Resources\CalendarResource\Widgets\CalendarWidget;

class CalendarPage extends BasePage
{
    // Configuración básica
    protected static string $view = 'pages.calendar-page';
    protected static string $resource = CalendarResource::class;

    // Registra los widgets
    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }

    // Opcional: Si necesitas pasar datos adicionales a la vista
    protected function getViewData(): array
    {
        return [
            'customData' => 'Valor personalizado',
        ];
    }
}