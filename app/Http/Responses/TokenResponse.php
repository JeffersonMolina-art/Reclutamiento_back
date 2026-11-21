<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;

class TokenResponse implements Responsable
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toResponse($request)
    {

        //token expiration en 15 minutos
        $expiracion = now()->addMinutes(350);
        $permisions = $this->user->getAllPermissions()->pluck('name')->toArray();

        $toke = $this->user->createToken(
            'App',
            $permisions,
            $expiracion
        );


        return response()->json([
            'token' => $toke->plainTextToken,
            'expiracion' => $toke->accessToken->expires_at,
            'permissions' => $toke->accessToken->abilities
        ]);
    }
}
