<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Postulacion;
use App\Models\PostulacionEstado;
use App\Models\Postulacionresultado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;

class OfertaApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver postulaciones', only: ['show']),
            new Middleware('abilities:editar postulaciones', only: ['store']),
        ];
    }
    public function store(Request $request, Postulacion $postulacion): JsonResponse
    {
        $validados = $request->validate([
            'salario_ofrecido' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'fecha_inicio_propuesta' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'condiciones_oferta' => ['required', 'string', 'max:5000'],
        ]);

        return $this->executeTransaction(function () use ($request, $postulacion, $validados) {
            $usuario = $request->user();

            $postulacion = Postulacion::query()
                ->visiblesPara($usuario)
                ->lockForUpdate()
                ->findOrFail($postulacion->getKey());

            if ($postulacion->estado_id !== PostulacionEstado::OFERTA) {
                throw ValidationException::withMessages([
                    'postulacion' => 'La postulación debe estar en la etapa de Oferta.',
                ]);
            }

            abort_unless($postulacion->puedeMoverPor($usuario), 403);

            if ($postulacion->resultado()->exists()) {
                throw ValidationException::withMessages([
                    'postulacion' => 'Ya se envió una oferta para esta postulación.',
                ]);
            }

            $oferta = $postulacion->resultado()->create([
                ...$validados,
                'estado_id' => $postulacion->estado_id,
                'user_id' => $usuario->id,
                'estado_oferta' => Postulacionresultado::OFERTA_PENDIENTE,
            ]);

            $postulacion->addBitacora(
                'Postulación',
                'Oferta enviada',
                'Se envió la oferta al candidato.',
                'tabler-send',
                'info',
                $usuario->id
            );

            return $this->sendResponse($oferta->toArray(), 'Oferta enviada con éxito.');
        });
    }
    public function show(Request $request, Postulacion $postulacion): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $postulacion) {
            $oferta = Postulacion::query()
                ->visiblesPara($request->user())
                ->findOrFail($postulacion->getKey())
                ->resultado;

            return $this->sendResponse($oferta?->toArray(), 'Oferta recuperada con éxito.');
        });
    }
}
