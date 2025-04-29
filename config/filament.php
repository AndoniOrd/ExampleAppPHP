<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | Aquí se registran todos los recursos de Filament (tablas CRUD).
    | Asegúrate de agregar tus recursos personalizados y de paquetes.
    |
    */

    'resources' => [ // ✅ Sección añadida
        App\Filament\Resources\EventResource::class, // Tu recurso personalizado
        // \Vormkracht10\FilamentMails\Resources\MailResource::class, // Ejemplo de paquete
        // \Vormkracht10\FilamentMails\Resources\EventResource::class, // ¡Cuidado con nombres duplicados!
    ],
'panel' => [
    'default' => [
        'pages' => [
            \App\Filament\Pages\ListDashboards::class,
        ],
    ],
],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | Configuración de broadcasting para websockets (Pusher/Laravel Echo).
    |
    */

    'broadcasting' => [
        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'authEndpoint' => '/broadcasting/auth',
        //     'disableStats' => true,
        //     'encrypted' => true,
        //     'forceTLS' => true,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Assets Path
    |--------------------------------------------------------------------------
    */
    'assets_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    */
    'cache_path' => base_path('bootstrap/cache/filament'),

    /*
    |--------------------------------------------------------------------------
    | Livewire Loading Delay
    |--------------------------------------------------------------------------
    */
    'livewire_loading_delay' => 'default',
];