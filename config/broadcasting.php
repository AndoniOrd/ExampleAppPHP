<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | El driver por defecto se controla con la variable de entorno
    | BROADCAST_DRIVER. En tu .env ya lo tienes como "pusher".
    |
    */

    'default' => env('BROADCAST_DRIVER', 'null'),  // :contentReference[oaicite:0]{index=0}

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Define aquí todos los drivers de broadcasting que uses en tu app.
    |
    */

    'connections' => [

        // ---------------------------------------------------------------------
        // Pusher (o BeyondCode Laravel WebSockets)
        // ---------------------------------------------------------------------
        'pusher' => [
            'driver'   => 'pusher',
            'key'      => env('PUSHER_APP_KEY'),
            'secret'   => env('PUSHER_APP_SECRET'),
            'app_id'   => env('PUSHER_APP_ID'),
            'options'  => [
                // Cluster si usas Pusher.com
                'cluster'   => env('PUSHER_APP_CLUSTER', 'mt1'),
                // Para web‑sockets locales
                'host'      => env('PUSHER_HOST', '127.0.0.1'),
                'port'      => env('PUSHER_PORT', 6001),
                'scheme'    => env('PUSHER_SCHEME', 'http'),
                'useTLS'    => env('PUSHER_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Opciones Guzzle si necesitas proxy, etc.
            ],
        ],  // :contentReference[oaicite:1]{index=1}

        // ---------------------------------------------------------------------
        // Ably (otro proveedor soportado)
        // ---------------------------------------------------------------------
        'ably' => [
            'driver' => 'ably',
            'key'    => env('ABLY_KEY'),
        ],  // :contentReference[oaicite:2]{index=2}

        // ---------------------------------------------------------------------
        // Log driver (útil para desarrollo/debug)
        // ---------------------------------------------------------------------
        'log' => [
            'driver' => 'log',
        ],

        // ---------------------------------------------------------------------
        // Null driver (deshabilita broadcasting)
        // ---------------------------------------------------------------------
        'null' => [
            'driver' => 'null',
        ],

    ],

];
