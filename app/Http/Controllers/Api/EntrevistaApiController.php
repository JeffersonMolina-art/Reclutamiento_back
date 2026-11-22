<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Entrevista;
use App\Models\EntrevistaEstado;
use App\Models\Postulacion;
use App\Models\PostulacionEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EntrevistaApiController extends AppBaseController implements HasMiddleware
{
    private const REGEX_GOOGLE_MEET = '~^https://meet\.google\.com/[a-z]{3}-[a-z]{4}-[a-z]{3}(\?\S*)?$~i';

    public static function middleware(): array
    {
        return [
            new Middleware('abilities:editar postulaciones', only: ['store', 'resultado']),
        ];
    }

    public function store(Request $request, Postulacion $postulacion): JsonResponse
    {
        $esVirtual = $request->input('modalidad') === 'virtual';

        $validados = $request->validate([
            'fecha_hora_programada' => ['required', 'date_format:Y-m-d H:i', 'after:now'],
            'modalidad' => ['required', Rule::in(['presencial', 'virtual'])],
            'ubicacion_o_enlace' => ['required', 'string', 'max:500', Rule::when($esVirtual, ['regex:'.self::REGEX_GOOGLE_MEET])],
            'observaciones' => ['nullable', 'string', 'max:255'],
            'tipo_entrevista' => ['required', Rule::in(['tecnica', 'recursos_humanos'])],
            'entrevistador_id' => ['nullable', 'integer'],
        ], [
            'fecha_hora_programada.after' => 'La entrevista debe programarse para una fecha futura.',
            'ubicacion_o_enlace.regex' => 'Ingresa un enlace válido de Google Meet.',
        ]);

        return $this->executeTransaction(function () use ($request, $postulacion, $validados) {
            $usuario = $request->user();

            $postulacion = Postulacion::query()
                ->visiblesPara($usuario)
                ->lockForUpdate()
                ->findOrFail($postulacion->getKey());

            if ($postulacion->estado_id !== PostulacionEstado::ENTREVISTA) {
                throw ValidationException::withMessages([
                    'postulacion' => 'La postulación debe estar en la etapa de Entrevista.',
                ]);
            }

            abort_unless($postulacion->puedeMoverPor($usuario), 403);

            $entrevista = $postulacion->entrevistas()->create([
                ...$validados,
                'entrevistador_id' => $validados['entrevistador_id'] ?? $usuario->id,
                'estado_id' => EntrevistaEstado::PROGRAMADA,
                'resultado' => Entrevista::RESULTADO_PENDIENTE,
            ]);

            $postulacion->addBitacora(
                'Postulación',
                'Entrevista programada',
                "Entrevista {$validados['modalidad']} programada para el {$validados['fecha_hora_programada']}.",
                'tabler-calendar-event',
                'info',
                $usuario->id
            );

            return $this->sendResponse(
                $entrevista->load('estado', 'entrevistador')->toArray(),
                'Entrevista programada con éxito.'
            );
        });
    }

    public function resultado(Request $request, Entrevista $entrevista): JsonResponse
    {
        $validados = $request->validate([
            'resultado' => ['required', Rule::in([Entrevista::RESULTADO_APROBADO, Entrevista::RESULTADO_NO_APROBADO])],
            'observaciones' => ['nullable', 'string', 'max:255'],
        ]);

        return $this->executeTransaction(function () use ($request, $entrevista, $validados) {
            $usuario = $request->user();

            $postulacion = Postulacion::query()
                ->visiblesPara($usuario)
                ->findOrFail($entrevista->postulacion_id);

            abort_unless($postulacion->puedeMoverPor($usuario), 403);

            $entrevista->update([
                'resultado' => $validados['resultado'],
                'observaciones' => $validados['observaciones'] ?? $entrevista->observaciones,
                'estado_id' => EntrevistaEstado::REALIZADA,
            ]);

            $aprobada = $validados['resultado'] === Entrevista::RESULTADO_APROBADO;

            $postulacion->addBitacora(
                'Postulación',
                'Resultado de entrevista',
                'Entrevista '.($aprobada ? 'aprobada' : 'no aprobada').'.',
                'tabler-checklist',
                $aprobada ? 'success' : 'warning',
                $usuario->id
            );

            return $this->sendResponse(
                $entrevista->load('estado', 'entrevistador')->toArray(),
                'Resultado de la entrevista registrado con éxito.'
            );
        });
    }
}
