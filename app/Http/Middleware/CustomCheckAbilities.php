<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomCheckAbilities
{
    public function handle($request, Closure $next, ...$abilities)
    {
        $user = $request->user();

        // Si es Super Administrador, permite el acceso sin validar habilidades
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Validación estándar de Sanctum
        return app(\Laravel\Sanctum\Http\Middleware\CheckAbilities::class)
            ->handle($request, $next, ...$abilities);
    }
}
