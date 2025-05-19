<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Registra las rutas necesarias para broadcasting:
        Broadcast::routes(['middleware' => ['auth:sanctum']]);
        // O si usas auth:api / session:
        // Broadcast::routes();

        /*
         * Aquí cargamos el archivo where defines tus canales,
         * por ejemplo: routes/channels.php
         */
        require base_path('routes/channels.php');
    }
}
