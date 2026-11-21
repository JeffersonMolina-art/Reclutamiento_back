<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SIGUA\Asociado;
use App\Models\User;
use App\Notifications\NotificacionBase;
use App\Traits\SMSTigoTrait;
use Illuminate\Http\Request;

class PruebaApiController extends Controller
{
    use SMSTigoTrait;

    public function index()
    {
        dd(Asociado::whereCif('579194')->first());
    }

    public function enviarNotificacion(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
//            'titulo' => 'required|string',
//            'cuerpo' => 'required|string',
//            'saludo' => 'nullable|string',
//            'agradecimiento' => 'nullable|string',
//            'subject' => 'nullable|string',
        ]);

        try {
            // Buscar el usuario al que se enviará la notificación
            $usuario = User::findOrFail($request->user_id);

            // Enviar la notificación
            $usuario->notify(new NotificacionBase());

            return response()->json([
                'success' => true,
                'message' => 'Notificación enviada con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la notificación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function enviarSMSTelefono(Request $request)
    {
        $request->validate([
            'telefono' => 'required|string|max:8',
            'mensaje' => 'required|string|max:160',
        ]);

        try {
            $telefono = $request->telefono;
            $mensaje = $request->mensaje;

            $this->sendTelefonoSMS($telefono, $mensaje);

            return response()->json([
                'success' => true,
                'message' => 'SMS enviado con éxito.',
                'mensje' => $mensaje
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el SMS.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
