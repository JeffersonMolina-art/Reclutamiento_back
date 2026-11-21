<?php

namespace App\Http\Controllers\Api\Publico;

use App\Http\Controllers\AppBaseController;
use App\Models\Candidato;
use App\Models\Postulacion;
use App\Models\PostulacionEstado;
use App\Models\SeguimientoPostulacion;
use App\Models\UserExterno;
use App\Models\Vacante;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostulacionCandidatoApiController extends AppBaseController
{
    /**
     * GET /api/portal/postulaciones
     */
    public function index(Request $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $candidato = $this->candidato($request);

            $postulaciones = Postulacion::query()
                ->where('candidato_id', $candidato->id)
                ->with(['vacante:id,nombre,puesto_id,sede_id,modalidad_trabajo', 'vacante.puesto:id,nombre', 'vacante.sede:id,nombre', 'estado:id,nombre,codigo'])
                ->orderByDesc('fecha_postulacion')
                ->orderByDesc('id')
                ->get()
                ->map(fn (Postulacion $p) => [
                    'id' => $p->id,
                    'vacante_id' => $p->vacante_id,
                    'vacante' => $p->vacante?->nombre,
                    'puesto' => $p->vacante?->puesto?->nombre,
                    'sede' => $p->vacante?->sede?->nombre,
                    'modalidad_trabajo' => $p->vacante?->modalidad_trabajo,
                    'estado' => $p->estado?->nombre,
                    'estado_codigo' => $p->estado?->codigo,
                    'fecha_postulacion' => $p->fecha_postulacion?->toDateString(),
                ])
                ->values();

            return response()->json(['data' => $postulaciones]);
        });
    }

    public function tracking(Request $request, int $postulacion): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $postulacion) {
            $candidato = $this->candidato($request);

            $registro = Postulacion::query()
                ->where('id', $postulacion)
                ->where('candidato_id', $candidato->id)
                ->with(['vacante:id,nombre', 'estado:id,nombre,codigo'])
                ->first();

            if (! $registro) {
                return response()->json(['message' => 'Postulación no encontrada.'], 404);
            }

            $historial = $registro->seguimientos()
                ->with('estado:id,nombre,codigo')
                ->orderBy('fecha_cambio')
                ->orderBy('id')
                ->get()
                ->map(fn (SeguimientoPostulacion $s) => [
                    'estado' => $s->estado?->nombre,
                    'estado_codigo' => $s->estado?->codigo,
                    'porcentaje' => $s->porcentaje,
                    'fecha' => $s->fecha_cambio?->toIso8601String(),
                ])
                ->values();

            return response()->json([
                'data' => [
                    'id' => $registro->id,
                    'vacante' => $registro->vacante?->nombre,
                    'fecha_postulacion' => $registro->fecha_postulacion?->toDateString(),
                    'estado_actual' => $registro->estado?->nombre,
                    'estado_codigo' => $registro->estado?->codigo,
                    'porcentaje' => $historial->last()['porcentaje'] ?? 0,
                    'historial' => $historial,
                ],
            ]);
        });
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate(['vacante_id' => 'required|integer|min:1']);

        return $this->executeTransaction(function () use ($request, $datos) {
            $candidato = $this->candidato($request);

            if (! $candidato->cvVigente()) {
                return response()->json([
                    'message' => $candidato->cvActual()
                        ? 'Tu CV tiene más de un año. Súbelo de nuevo para poder postularte.'
                        : 'Sube tu CV antes de postularte.',
                    'requiere_cv' => true,
                ], 422);
            }

            $vacante = Vacante::query()->publicas()->find($datos['vacante_id']);

            if (! $vacante) {
                return response()->json(['message' => 'La vacante no está disponible.'], 422);
            }

            $existente = Postulacion::where('vacante_id', $vacante->id)
                ->where('candidato_id', $candidato->id)
                ->exists();

            if ($existente) {
                return response()->json(['message' => 'Ya te postulaste a esta vacante.'], 409);
            }

            try {
                $postulacion = Postulacion::create([
                    'vacante_id' => $vacante->id,
                    'candidato_id' => $candidato->id,
                    'estado_id' => Postulacion::ESTADO_INICIAL,
                    'fecha_postulacion' => now()->toDateString(),
                    'fuente_reclutamiento' => 'portal_empleo',
                ]);

                if ($vacante->user_encargado) {
                    $postulacion->asignacion()->create([
                        'users_id' => $vacante->user_encargado,
                        'bandeja_id' => $postulacion->estado()->value('bandeja_id'),
                        'nombre' => Str::limit($vacante->encargado?->nombre_completo ?? '', 45, ''),
                    ]);
                }

            } catch (UniqueConstraintViolationException) {
                return response()->json(['message' => 'Ya te postulaste a esta vacante.'], 409);
            }

            $postulacion->registrarSeguimiento(Postulacion::ESTADO_INICIAL, null, 'Postulación recibida', null, 0);

            return response()->json([
                'message' => 'Te postulaste correctamente.',
                'id' => $postulacion->id,
            ], 201);
        });
    }

    private function candidato(Request $request): Candidato
    {
        /** @var UserExterno $usuario */
        $usuario = $request->user();

        return $usuario->candidato()->firstOrFail();
    }
}
