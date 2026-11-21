<?php

namespace App\Providers;

use App\Exceptions\ApiExceptionHandler;
use App\Models\PersonalAccessToken;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar ApiExceptionHandler como el manejador de excepciones predeterminado
        $this->app->singleton(ExceptionHandler::class, ApiExceptionHandler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

    }
}
