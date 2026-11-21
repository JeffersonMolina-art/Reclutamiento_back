<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Postulacion;
use App\Models\PostulacionEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostulacionApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver postulaciones', only: ['index', 'show', 'cv']),
            new Middleware('abilities:editar postulaciones', only: ['update', 'comentar']),
            new Middleware('abilities:asignar postulaciones', only: ['asignar']),
        ];
    }

    /**
     * GET|HEAD /reclutamiento/postulaciones
     */
    public function index(Request $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $consulta = Postulacion::query()
                ->visiblesPara($request->user())
                ->with(['vacante', 'candidato', 'estado.bandeja', 'asignacion']);

            $postulaciones = QueryBuilder::for($consulta, $request)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    AllowedFilter::exact('vacante_id'),
                    AllowedFilter::exact('candidato_id'),
                    AllowedFilter::exact('estado_id'),
                    AllowedFilter::exact('fuente_reclutamiento'),
                    AllowedFilter::scope('bandeja', 'enBandeja'),
                    AllowedFilter::callback('asignadas_a_mi', fn ($q) => $q->whereHas(
                        'asignacion',
                        fn ($a) => $a->where('users_id', $request->user()->id)
                    )),
                    AllowedFilter::callback('fecha_desde', fn ($q, $valor) => $q->whereDate('fecha_postulacion', '>=', $valor)),
                    AllowedFilter::callback('fecha_hasta', fn ($q, $valor) => $q->whereDate('fecha_postulacion', '<=', $valor)),
                )
                ->allowedSorts('id', 'vacante_id', 'estado_id', 'fecha_postulacion', 'created_at')
                ->allowedIncludes('vacante', 'candidato', 'estado', 'seguimientos', 'asignacion')
                ->defaultSort('-id')
                ->jsonPaginate($request->input('page.size') ?? 10);

            return $this->sendResponse(
                $postulaciones->toArray(),
                'Postulaciones recuperadas con éxito.'
            );
        });
    }

    /**
     * GET|HEAD /reclutamiento/postulaciones/{postulacion}
     */
    public function show(Request $request, Postulacion $postulacion): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $postulacion) {
            $detalle = Postulacion::query()
                ->visiblesPara($request->user())
                ->with([
                    'vacante',
                    'candidato.conocimientos',
                    'candidato.experiencias',
                    'candidato.formaciones',
                    'estado.bandeja',
                    'asignacion',
                    'seguimientos',
                    'bitacoras',
                ])
                ->findOrFail($postulacion->getKey());

            $datos = $detalle->toArray();
            $cv = $detalle->candidato?->cvActual();

            $datos['cv'] = $cv ? [
                'tiene_cv' => true,
                'extension' => pathinfo($cv->file_name, PATHINFO_EXTENSION),
                'tamano' => $cv->size,
                'subido_el' => $cv->created_at?->toIso8601String(),
            ] : ['tiene_cv' => false];

            return $this->sendResponse(
                $datos,
                'Postulación recuperada con éxito.'
            );
        });
    }

    /**
     * Descarga el CV del candidato de la postulación.
     * GET /reclutamiento/postulaciones/{postulacion}/cv
     */
    public function cv(Request $request, Postulacion $postulacion)
    {
        $postulacion = Postulacion::query()
            ->visiblesPara($request->user())
            ->with('candidato')
            ->findOrFail($postulacion->getKey());

        $cv = $postulacion->candidato?->cvActual();

        if (! $cv) {
            return response()->json(['message' => 'El candidato no tiene CV cargado.'], 404);
        }

        return $cv->toResponse($request);
    }

    /**
     * Cambia el estado de la postulación y registra el seguimiento.
     * PUT/PATCH /reclutamiento/postulaciones/{postulacion}
     */
    public function update(Request $request, Postulacion $postulacion): JsonResponse
    {
        $validados = $request->validate([
            'estado_id' => ['required', 'integer'],
            'comentario' => ['required', 'string', 'max:255'],
            'porcentaje' => ['nullable', 'numeric', 'between:0,100'],
            'puntaje_fase' => ['nullable', 'numeric'],
        ]);

        return $this->executeTransaction(function () use ($request, $postulacion, $validados) {
            $postulacion = Postulacion::query()
                ->visiblesPara($request->user())
                ->findOrFail($postulacion->getKey());

            $postulacion->moverA(
                PostulacionEstado::findOrFail($validados['estado_id']),
                $validados['comentario'],
                $request->user()->id,
                $validados['porcentaje'] ?? null,
                $validados['puntaje_fase'] ?? null
            );

            return $this->sendResponse(
                $postulacion->load('vacante', 'candidato', 'estado', 'seguimientos', 'asignacion')->toArray(),
                'Postulación actualizada con éxito.'
            );
        });
    }

    /**
     * Agrega un comentario a la bitácora de la postulación.
     * POST /reclutamiento/postulaciones/{postulacion}/comentarios
     */
    public function comentar(Request $request, Postulacion $postulacion): JsonResponse
    {
        $validados = $request->validate([
            'comentario' => ['required', 'string', 'max:1000'],
        ]);

        return $this->executeTransaction(function () use ($request, $postulacion, $validados) {
            $postulacion = Postulacion::query()
                ->visiblesPara($request->user())
                ->findOrFail($postulacion->getKey());

            $postulacion->comentar($validados['comentario'], $request->user()->id);

            return $this->sendResponse(
                $postulacion->load('bitacoras')->toArray(),
                'Comentario agregado con éxito.'
            );
        });
    }

    /**
     * Asigna o reasigna la postulación a un reclutador.
     * POST /reclutamiento/postulaciones/{postulacion}/asignar
     */
    public function asignar(Request $request, Postulacion $postulacion): JsonResponse
    {
        $validados = $request->validate([
            'users_id' => ['required', 'integer'],
        ]);

        return $this->executeTransaction(function () use ($request, $postulacion, $validados) {
            $postulacion = Postulacion::query()
                ->visiblesPara($request->user())
                ->findOrFail($postulacion->getKey());

            $postulacion->asignarA($validados['users_id'], $request->user()->id);

            return $this->sendResponse(
                $postulacion->load('vacante', 'candidato', 'estado', 'seguimientos', 'asignacion')->toArray(),
                'Postulación asignada con éxito.'
            );
        });
    }
}
