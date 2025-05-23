<?php

namespace App\Providers;

use App\Models\CustomToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       /*if (env(key: 'APP_ENV') !=='local') {
            URL::forceScheme(scheme:'https');
          }
          */  
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(CustomToken::class);
    }
}
