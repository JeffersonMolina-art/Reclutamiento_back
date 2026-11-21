<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\AppBaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Log;
use Throwable;

class AuthAzureApiController extends AppBaseController
{
    public function login(Request $request)
    {
        try {
            $input = $request->all();

            $soloNick = explode('@', $input['email'])[0];

            $usuarioLaravel = User::where('usuario_dominio', $soloNick)->first();

            $token = $usuarioLaravel->createToken('AzureToken')->plainTextToken;

            $usuarioLaravel->update([
                'ultima_autenticacion' => User::autenticacionPorAzure
            ]);

            return $this->sendResponse($token, 'Token created');

        } catch (Throwable $e) {
            return $this->sendError(manejarException($e));
        }

    }


}
