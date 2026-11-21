<?php

namespace App\Http\Controllers\Api\Publico;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Publico\LoginCandidatoRequest;
use App\Models\UserExterno;
use App\Services\LoginRateLimiterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CandidatoAuthApiController extends AppBaseController
{
    private const MENSAJE_CREDENCIALES = 'Correo o contraseña incorrectos.';

    /**
     * POST /api/publico/candidatos/login
     */
    public function login(LoginCandidatoRequest $request, LoginRateLimiterService $limitador): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $limitador) {
            $email = $request->input('email');
            $clave = 'candidato|' . $request->ip() . '|' . $email;

            if ($limitador->isBlocked($clave)) {
                return response()->json([
                    'message' => 'Demasiados intentos fallidos. Intente de nuevo más tarde.',
                    'segundos_restantes' => $limitador->getBlockedSeconds($clave),
                ], 429);
            }

            $usuario = UserExterno::where('email', $email)->first();

            $hash = $usuario?->password;
            $valida = Hash::check($request->input('password'), $hash);

            if (! $usuario || ! $valida) {
                $limitador->recordFailedAttempt($clave);

                return response()->json(['message' => self::MENSAJE_CREDENCIALES], 401);
            }

            if ((int) $usuario->estado_id !== UserExterno::ESTADO_ACTIVO) {
                return response()->json(['message' => 'Tu cuenta no está activa.'], 403);
            }

            $limitador->clearAttempts($clave);

            $expira = now()->addHours(8);
            $token = $usuario->createToken('portal-candidato', ['candidato'], $expira);

            return response()->json([
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $expira->toIso8601String(),
                'candidato' => $this->perfil($usuario),
            ]);
        });
    }

    /**
     * GET /api/portal/me
     */
    public function me(Request $request): JsonResponse
    {
        return $this->executeOperation(fn () => response()->json($this->perfil($request->user())));
    }

    /**
     * POST /api/portal/logout
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Sesión cerrada.']);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function perfil(UserExterno $usuario): array
    {
        $candidato = $usuario->candidato;

        return [
            'id' => $usuario->id,
            'email' => $usuario->email,
            'telefono' => $usuario->telefono,
            'nombres' => $candidato
                ? trim(implode(' ', array_filter([$candidato->primer_nombre, $candidato->segundo_nombre, $candidato->tercer_nombre])))
                : null,
            'apellidos' => $candidato
                ? trim(implode(' ', array_filter([$candidato->primer_apellido, $candidato->segundo_apellido])))
                : null,
            'email_verificado' => $usuario->email_verified_at !== null,
        ];
    }
}
