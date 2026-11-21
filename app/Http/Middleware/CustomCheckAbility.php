<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

namespace App\Http\Middleware;

class CustomCheckAbility
{
    public function handle($request, Closure $next, $ability)
    {
        $user = $request->user();

        // Si es Super Administrador, permite el acceso sin validar habilidades
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Validación estándar de Sanctum
        return app(\Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class)
            ->handle($request, $next, $ability);
    }
}

