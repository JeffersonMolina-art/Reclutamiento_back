<?php

namespace App\Http\Middleware;

use App\Models\UserExterno;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloCandidato
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (! $usuario instanceof UserExterno || ! $usuario->tokenCan('candidato')) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return $next($request);
    }
}
