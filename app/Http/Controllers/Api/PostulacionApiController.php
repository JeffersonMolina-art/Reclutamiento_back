<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\PostulacionDetalleResource;
use App\Models\Postulacion;
use App\Models\PostulacionEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostulacionApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver postulaciones', only: ['index', 'show', 'cv', 'contacto', 'listarArchivos', 'descargarArchivo']),
            new Middleware('abilities:editar postulaciones', only: ['update', 'comentar', 'subirArchivo', 'eliminarArchivo']),
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
                    'vacante.puesto',
                    'vacante.sede',
                    'vacante.requisitos',
                    'candidato.conocimientos',
                    'candidato.experiencias',
                    'candidato.formaciones',
                    'estado',
                ])
                ->findOrFail($postulacion->getKey());

            $cv = $detalle->candidato?->cvActual();

            $datos = (new PostulacionDetalleResource($detalle))->resolve();

            $datos['estados_disponibles'] = PostulacionEstado::query()
                ->whereKey($detalle->estadosDisponibles())
                ->get(['id', 'codigo', 'nombre'])
                ->toArray();

            $datos['entrevistas'] = $detalle->entrevistas()
                ->with('estado')
                ->orderByDesc('id')
                ->get()
                ->toArray();

            $datos['cv'] = $cv ? [
                'tiene_cv' => true,
                'extension' => pathinfo($cv->file_name, PATHINFO_EXTENSION),
            ] : ['tiene_cv' => false];

            return $this->sendResponse($datos, 'Postulación recuperada con éxito.');
        });
    }
    public function subirArchivo(Request $request, Postulacion $postulacion): JsonResponse
    {
        $validados = $request->validate([
            'coleccion' => ['required', 'string', Rule::in(Postulacion::COLECCIONES_EXAMENES)],
            'archivo' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'puntuacion' => ['required', 'numeric', 'min:0'],
            'modalidad' => ['required', 'string', 'max:45'],
            'comentarios' => ['nullable', 'string', 'max:255'],
            'entrevistador_id' => ['nullable', 'integer'],
        ], [
            'archivo.mimes' => 'El archivo debe ser PDF, imagen, DOC o DOCX.',
            'archivo.max' => 'El archivo no debe pesar más de 10 MB.',
        ]);

        return $this->executeTransaction(function () use ($request, $postulacion, $validados) {
            $usuario = $request->user();

            $postulacion = Postulacion::query()
                ->visiblesPara($usuario)
                ->findOrFail($postulacion->getKey());

            $evaluacion = $postulacion->evaluaciones()->create([
                'entrevistador_id' => $validados['entrevistador_id'] ?? $usuario->id,
                'puntuacion' => $validados['puntuacion'],
                'modalidad' => $validados['modalidad'],
                'fecha_evaluacion' => now(),
                'comentarios' => $validados['comentarios'] ?? null,
                'users_id' => $usuario->id,
                'postulacion_estado_id' => $postulacion->estado_id,
            ]);

            $postulacion->guardarArchivo($request->file('archivo'), $validados['coleccion'], $usuario, $evaluacion);

            return $this->sendResponse(
                $postulacion->fresh()->archivos($validados['coleccion']),
                'Archivo cargado correctamente.'
            );
        });
    }

    public function listarArchivos(Request $request, Postulacion $postulacion): JsonResponse
    {
        $validados = $request->validate([
            'coleccion' => ['nullable', 'string', Rule::in(Postulacion::COLECCIONES_EXAMENES)],
        ]);

        return $this->executeOperation(function () use ($request, $postulacion, $validados) {
            $postulacion = Postulacion::query()
                ->visiblesPara($request->user())
                ->findOrFail($postulacion->getKey());

            return $this->sendResponse(
                $postulacion->archivos($validados['coleccion'] ?? null),
                'Archivos recuperados con éxito.'
            );
        });
    }


    public function eliminarArchivo(Request $request, Postulacion $postulacion, int $media): JsonResponse
    {
        return $this->executeTransaction(function () use ($request, $postulacion, $media) {
            $postulacion = Postulacion::query()
                ->visiblesPara($request->user())
                ->findOrFail($postulacion->getKey());

            $archivo = $postulacion->media()->findOrFail($media);

            $postulacion->evaluaciones()->whereKey($archivo->getCustomProperty('evaluacion_id'))->delete();
            $archivo->delete();

            return $this->sendResponse([], 'Archivo eliminado correctamente.');
        });
    }

    /**
     * Descarga un archivo de la postulación.
     * GET /reclutamiento/postulaciones/{postulacion}/archivos/{media}
     */
    public function descargarArchivo(Request $request, Postulacion $postulacion, int $media)
    {
        $postulacion = Postulacion::query()
            ->visiblesPara($request->user())
            ->findOrFail($postulacion->getKey());

        return $postulacion->media()->findOrFail($media)->toResponse($request);
    }

    public function contacto(Request $request, Postulacion $postulacion): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $postulacion) {
            $candidato = Postulacion::query()
                ->visiblesPara($request->user())
                ->with('candidato')
                ->findOrFail($postulacion->getKey())
                ->candidato;

            return $this->sendResponse([
                'nombre' => collect([
                    $candidato->primer_nombre,
                    $candidato->segundo_nombre,
                    $candidato->tercer_nombre,
                    $candidato->primer_apellido,
                    $candidato->segundo_apellido,
                ])->filter()->implode(' '),
                'correo' => $candidato->correo_contacto,
                'telefono' => $candidato->telefono_contacto,
            ], 'Contacto del candidato recuperado con éxito.');
        });
    }

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
