<?php

namespace App\Providers;

use App\Exceptions\ApiExceptionHandler;
use App\Models\PersonalAccessToken;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('publico', fn (Request $request) => Limit::perMinute(60)->by('publico|' . $request->ip()));

        RateLimiter::for('registro-candidato', fn (Request $request) => [
            Limit::perMinute(5)->by('registro|ip|' . $request->ip()),
            Limit::perHour(20)->by('registro|ip-hora|' . $request->ip()),
            Limit::perHour(5)->by('registro|email|' . mb_strtolower((string) $request->input('email'))),
        ]);
        RateLimiter::for('login-candidato', fn (Request $request) => [
            Limit::perMinute(10)->by('login|ip|' . $request->ip()),
            Limit::perMinute(5)->by('login|ip-email|' . $request->ip() . '|' . mb_strtolower((string) $request->input('email'))),
        ]);

    }
}
