<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\AppBaseController;

use App\Http\Responses\TokenResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Milon\Barcode\DNS2D;
use PragmaRX\Google2FA\Google2FA;


class AuthDobleFactorApiController extends AppBaseController
{
    public function crearQr(User $user)
    {

        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey();

        $user->two_factor_secret = encrypt($secret);

        $user->save();

        $companyName = "Go Credit";

        $qrUrl = "otpauth://totp/{$companyName}:{$user->email}?secret={$secret}&issuer={$companyName}";

        $generadorWeb = new DNS2D();

        $qrCode = $generadorWeb->getBarcodePNG($qrUrl, "QRCODE", 4, 4);

        return $this->sendResponse([
            'qr_code' => $qrCode,
        ], 'Código QR generado correctamente');

    }

    public function confirmarDobleFactor(Request $request)
    {
        $user = User::find($request->user_id);

        $request->validate([
            'codigo' => 'required|digits:6', // Código de 6 dígitos
            'user_id' => 'required',
        ]);

        $google2fa = new Google2FA();

        $google2fa->setWindow(0);

        if (!$google2fa->verifyKey(decrypt($user->two_factor_secret), $request->codigo)) {
            return $this->sendError('El código ingresado no es correcto.');
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now()
        ])->save();

        if ($request->autenticar_usuario) {

            if(!$user->two_factor_pending){

                return $this->sendError('Debe de tener ambos factores activos');

            }

            $user->forceFill([
                'two_factor_pending' => false,
            ])->save();

            return new TokenResponse($user);

        }

        return $this->sendSuccess('Autenticación de dos factores validada con éxito.');

    }

    public function validarCodigoAuth(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user->two_factor_pending) {

            return $this->sendError('Se requiere autenticación de dos factores');

        }

        $request->validate([
            'codigo' => 'required|digits:6',
            'user_id' => 'required',
        ]);

        $google2fa = new Google2FA();

        $google2fa->setWindow(0);

        if (!$google2fa->verifyKey(decrypt($user->two_factor_secret), $request->codigo)) {
            return $this->sendError('El código ingresado no es correcto.');
        }

        $user->update([
            'two_factor_pending' => false,
        ]);

        return new TokenResponse($user);

    }

    public function reiniciarDobleFactor(User $user)
    {

        try {

            $user->two_factor_secret = null;
            $user->two_factor_confirmed_at = null;
            $user->two_factor_pending = false;
            $user->save();

        } catch (\Exception $e) {

            return $this->sendError('Error al reiniciar la autenticación de dos factores.');

        }

        return $this->sendSuccess('Autenticación de dos factores reiniciada con éxito.');

    }
}
