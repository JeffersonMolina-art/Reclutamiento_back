<?php

namespace App\Traits;

use Laravel\Sanctum\PersonalAccessToken;

trait UserPermisosTrait
{
    public function obtenerTokenAppActivo(): ?PersonalAccessToken
    {
        return $this->tokens()
            ->where('name', 'App')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();
    }

    public function obtenerTokenActual(): ?PersonalAccessToken
    {
        return $this->currentAccessToken();
    }

    public function obtenerPermisosComoAbilities(): array
    {
        return $this->getAllPermissions()
            ->pluck('name')
            ->values()
            ->toArray();
    }

    public function actualizarAbilitiesToken(?PersonalAccessToken $token = null): bool
    {
        $token = $token ?: $this->obtenerTokenAppActivo();

        if (!$token) {
            return false;
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            return false;
        }

        $token->forceFill([
            'abilities' => $this->obtenerPermisosComoAbilities(),
        ])->save();

        return true;
    }

    public function actualizarAbilitiesTokenAppActivo(): bool
    {
        return $this->actualizarAbilitiesToken(
            $this->obtenerTokenAppActivo()
        );
    }

    public function actualizarAbilitiesTodosLosTokensAppActivos(): int
    {
        $tokens = $this->tokens()
            ->where('name', 'App')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get();

        if ($tokens->isEmpty()) {
            return 0;
        }

        $abilities = $this->obtenerPermisosComoAbilities();
        $actualizados = 0;

        foreach ($tokens as $token) {
            $token->forceFill([
                'abilities' => $abilities,
            ])->save();

            $actualizados++;
        }

        return $actualizados;
    }
}
