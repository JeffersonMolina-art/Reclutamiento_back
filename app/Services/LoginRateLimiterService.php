<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class LoginRateLimiterService
{
    private const MAX_ATTEMPTS = 3;
    private const ATTEMPT_WINDOW = 3600; // 1 hora

    /**
     * Verifica si la IP está bloqueada
     */
    public function isBlocked(string $ip): bool
    {
        return RateLimiter::tooManyAttempts($this->blockKey($ip), 1);
    }

    /**
     * Obtiene los segundos restantes de bloqueo
     */
    public function getBlockedSeconds(string $ip): int
    {
        return RateLimiter::availableIn($this->blockKey($ip));
    }

    /**
     * Obtiene el total de intentos realizados
     */
    public function getTotalAttempts(string $ip): int
    {
        return RateLimiter::attempts($this->ipKey($ip));
    }

    /**
     * Registra un intento fallido y retorna el total de intentos
     */
    public function recordFailedAttempt(string $ip): int
    {
        RateLimiter::hit($this->ipKey($ip), self::ATTEMPT_WINDOW);
        $attempts = $this->getTotalAttempts($ip);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->blockIp($ip, $attempts);

            // Log de seguridad
            Log::warning('IP bloqueada por intentos fallidos de login', [
                'ip' => $ip,
                'intentos' => $attempts,
                'bloqueo_minutos' => $this->getBlockMinutes($attempts)
            ]);
        }

        return $attempts;
    }

    /**
     * Limpia todos los intentos de una IP
     */
    public function clearAttempts(string $ip): void
    {
        RateLimiter::clear($this->ipKey($ip));
        RateLimiter::clear($this->blockKey($ip));
    }

    /**
     * Obtiene los intentos restantes antes del bloqueo
     */
    public function getRemainingAttempts(string $ip): int
    {
        return max(0, self::MAX_ATTEMPTS - $this->getTotalAttempts($ip));
    }

    /**
     * Calcula los minutos de bloqueo según los intentos
     */
    public function getBlockMinutes(int $attempts): int
    {
        return match($attempts) {
            3 => 1,
            5 => 5,
            7 => 15,
            10 => 60,
            default => 0,
        };
    }

    /**
     * Bloquea una IP por un tiempo determinado
     */
    private function blockIp(string $ip, int $attempts): void
    {
        $minutes = $this->getBlockMinutes($attempts);
        RateLimiter::hit($this->blockKey($ip), $minutes * 60);
    }

    /**
     * Genera la clave para los intentos de login
     */
    private function ipKey(string $ip): string
    {
        return "login:$ip";
    }

    /**
     * Genera la clave para el bloqueo
     */
    private function blockKey(string $ip): string
    {
        return "login_block:$ip";
    }
}
