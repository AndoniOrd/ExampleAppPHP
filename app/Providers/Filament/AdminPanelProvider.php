<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Vormkracht10\FilamentMails\Facades\FilamentMails;
use Vormkracht10\FilamentMails\FilamentMailsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->globalSearch(false)
            ->path('admin')
            ->login()
            ->favicon(asset('images/logo.jpg'))
            ->brandLogo(asset('images/logo.jpg'))
            //->viteTheme('public\css\filament\admin\theme.css')
            //asset('css/filament/admin/ckeditor-dark.css'),
            ->colors([
                'primary' => Color::Blue,     // Vibrant blue for main color
                'success' => Color::Emerald,  // Vibrant green for success
                'danger'  => Color::Rose,     // Vivid red-pink for errors
                'warning' => Color::Amber,    // Bright amber for warnings
                'info'    => Color::Sky,      // Soft vibrant light blue for info
                'gray'    => Color::Zinc,     // Clean neutral gray
                
            ])
            
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // Add Filament Mails routes
            ->routes(fn() => FilamentMails::routes())
            // Register the Filament Mails plugin
            ->plugin(FilamentMailsPlugin::make())
            // Register the Filament Full Calendar plugin
            ->plugin(
                FilamentFullCalendarPlugin::make()
                    ->schedulerLicenseKey(env('FILAMENT_FULL_CALENDAR_LICENSE_KEY') ?: '')
                    ->selectable(true) // Permite seleccionar días o franjas horarias al hacer clic y arrastrar.
                    ->editable(true)   // Permite arrastrar y redimensionar eventos.
                    ->timezone(config('app.timezone')) // Usa la zona horaria definida en tu app o cámbiala según tus necesidades.
                    ->locale(config('app.locale'))     // Usa el locale definido en tu app o cámbialo según corresponda.
                    ->plugins(
                        [
                            'interaction',  // Interacciones de arrastrar/soltar, etc.
                            'dayGrid',
                            'timeGrid',
                            'list',
                            'multiMonth',
                            'scrollGrid',
                            'timeline',
                            'adaptive',
                            'resource',
                            'resourceDayGrid',
                            'resourceTimeline',
                            'resourceTimeGrid',
                            'rrule',
                            'moment',
                            'momentTimezone'
                        ],
                        true // Si deseas fusionar estos plugins con los predeterminados, o false para reemplazarlos.
                    )
                    ->config([
                        // Configuraciones adicionales del calendario según la documentación de FullCalendar.
                        'firstDay' => 1,
                        'headerToolbar' => [
                            'left' => 'prev,next today',
                            'center' => 'title',
                            'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
                        ],
                        // Puedes agregar más configuraciones aquí según tus necesidades.
                    ])
            );
    }
}
