<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\TokenResponse;
use App\Models\LoginLog;
use App\Models\User;
use App\Services\LoginRateLimiterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthApiController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function login(LoginRequest $request, LoginRateLimiterService $rateLimiter): TokenResponse|JsonResponse
    {
        try {

            /*
             * Obtener el usuario desde el request.
             */
            $usuario_dominio = $request->input('usuario_dominio');

            // Verificar bloqueo
            if ($rateLimiter->isBlocked($usuario_dominio)) {
                LoginLog::registrar($request, [
                    'usuario_dominio' => $usuario_dominio,
                    'metodo' => LoginLog::METODO_CREDENCIALES,
                    'resultado' => LoginLog::RESULTADO_FALLIDO,
                    'motivo_fallo' => LoginLog::FALLO_BLOQUEADO,
                    'severity_level' => LoginLog::SEVERITY_CRITICAL,
                ]);
                return response()->json([
                    'message' => "Demasiados intentos fallidos en muy poco tiempo. Por favor intente de nuevo en ",
                    'total_intentos' => $rateLimiter->getTotalAttempts($usuario_dominio),
                    'segundos_restantes' => $rateLimiter->getBlockedSeconds($usuario_dominio)
                ], 429);
            }

            // Validar credenciales
            $user = $this->findAndValidateUser($request->only('usuario_dominio', 'password'));

            if (!$user) {
                $attempts = $rateLimiter->recordFailedAttempt($usuario_dominio);

                LoginLog::registrar($request, [
                    'usuario_dominio' => $usuario_dominio,
                    'metodo' => LoginLog::METODO_CREDENCIALES,
                    'resultado' => LoginLog::RESULTADO_FALLIDO,
                    'motivo_fallo' => LoginLog::FALLO_CREDENCIALES_INCORRECTAS,
                    'severity_level' => LoginLog::SEVERITY_WARNING,
                ]);

                return response()->json([
                    'message' => 'Usuario o contraseña incorrectos.',
                    'intentos_realizados' => $attempts,
                    'intentos_restantes' => $rateLimiter->getRemainingAttempts($usuario_dominio),
                ], 403);
            }

            if ($user->estado !== 'Activo') {
                LoginLog::registrar($request, [
                    'usuario_id' => $user->id,
                    'usuario_dominio' => $usuario_dominio,
                    'metodo' => LoginLog::METODO_CREDENCIALES,
                    'resultado' => LoginLog::RESULTADO_FALLIDO,
                    'motivo_fallo' => LoginLog::FALLO_USUARIO_INACTIVO,
                    'severity_level' => LoginLog::SEVERITY_WARNING,
                ]);
                return response()->json(['message' => 'Usuario inactivo.'], 403);
            }

            $user->update([
                'two_factor_pending' => true,
                'ultima_autenticacion' => \App\Models\User::autenticacionPorCredenciales,
            ]);

            $rateLimiter->clearAttempts($usuario_dominio);
            return new TokenResponse($user, $request);

        } catch (Throwable $e) {
            return $this->sendError(manejarException($e));
        }
    }
    private function findAndValidateUser(array $credentials): ?User
    {
        $user = User::where('usuario_dominio', $credentials['usuario_dominio'])
            ->select('id', 'usuario_dominio', 'password', 'estado',
                'two_factor_secret', 'two_factor_habilitado', 'two_factor_confirmed_at')
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $user;
    }
    public function logout(Request $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            \App\Models\LoginLog::where('session_id', $request->user()->currentAccessToken()->id)
                ->whereNull('logout_at')
                ->latest()
                ->first()
                ?->update(['logout_at' => now()]);

            $request->user()->currentAccessToken()->delete();

            if ($request->user()->ultima_autenticacion == 'azure') {
                $tenant = config('services.microsoft.tenant',
                    'common');
                $postLogoutRedirectUri = urlencode(config('services.frontend.url').'/login');

                $azureLogoutUrl = "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/logout?post_logout_redirect_uri={$postLogoutRedirectUri}";

                return $this->sendResponse([
                    'azure_logout_url' => $azureLogoutUrl,
                    'message' => 'Token deleted',
                ], 'Token deleted');
            }

            return $this->sendResponse([], 'Token deleted');
        });
    }

}
