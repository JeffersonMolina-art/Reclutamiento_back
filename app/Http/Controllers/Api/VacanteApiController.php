<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\CreateVacanteApiRequest;
use App\Http\Requests\Api\UpdateVacanteApiRequest;
use App\Models\Vacante;
use App\Models\VacanteEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class VacanteApiController
 */
class VacanteApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver vacantes', only: ['index', 'show']),
            new Middleware('abilities:crear vacantes', only: ['store']),
            new Middleware('abilities:editar vacantes', only: ['update']),
            new Middleware('abilities:eliminar vacantes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Vacantes.
     * GET|HEAD /vacantes
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $vacantes = QueryBuilder::for(Vacante::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    AllowedFilter::exact('estado_id'),
                    AllowedFilter::exact('puesto_id'),
                    AllowedFilter::exact('sede_id'),
                    AllowedFilter::exact('modalidad_trabajo'),
                    'nombre'
                )
                ->allowedSorts(
                    'id',
                    'nombre',
                    'estado_id',
                    'cantidad_plazas',
                    'fecha_publicacion',
                    'created_at'
                )
                ->allowedIncludes('estado', 'puesto', 'sede', 'requisitos', 'caracteristicas')
                ->defaultSort('-id')
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($vacantes->toArray(), 'vacantes recuperadas con éxito.');
        });
    }

    /**
     * Store a newly created Vacante in storage.
     * POST /vacantes
     */
    public function store(CreateVacanteApiRequest $request): JsonResponse
    {
        return $this->executeTransaction(function () use ($request) {
            $datos = $request->safe()->except(['requisitos', 'caracteristicas']);
            $datos['user_id'] = $request->user()->id;
            $datos = $this->completarFechaPublicacion($datos);

            $vacante = Vacante::create($datos);

            $this->sincronizarHijos($vacante, $request->validated());

            $vacante->addBitacora(
                'Vacante',
                'Vacante creada',
                $vacante->generarComentarioCreacion(),
                'tabler-plus',
                'success'
            );

            return $this->sendResponse($vacante->load('estado', 'puesto', 'sede', 'requisitos', 'caracteristicas')->toArray(), 'Vacante creada con éxito.');
        });
    }

    /**
     * Display the specified Vacante.
     * GET|HEAD /vacantes/{id}
     */
    public function show(Vacante $vacante): JsonResponse
    {
        return $this->executeOperation(function () use ($vacante) {
            $vacante->load('estado', 'puesto', 'sede', 'requisitos', 'caracteristicas', 'bitacoras');

            return $this->sendResponse($vacante->toArray(), 'Vacante recuperada con éxito.');
        });
    }

    /**
     * Update the specified Vacante in storage.
     * PUT/PATCH /vacantes/{id}
     */
    public function update(UpdateVacanteApiRequest $request, $id): JsonResponse
    {
        return $this->executeTransaction(function () use ($request, $id) {
            $vacante = Vacante::findOrFail($id);

            $datos = $this->completarFechaPublicacion(
                $request->safe()->except(['requisitos', 'caracteristicas']),
                $vacante
            );

            $vacante->fill($datos);
            $comentario = $vacante->generarComentarioActualizacion();
            $vacante->save();

            $validados = $request->validated();
            $this->sincronizarHijos($vacante, $validados);

            if (array_key_exists('requisitos', $validados)) {
                $comentario .= ' | Se actualizaron los requisitos';
            }
            if (array_key_exists('caracteristicas', $validados)) {
                $comentario .= ' | Se actualizaron las características';
            }

            $vacante->addBitacora(
                'Vacante',
                'Vacante actualizada',
                $comentario,
                'tabler-edit',
                'primary'
            );

            return $this->sendResponse($vacante->load('estado', 'puesto', 'sede', 'requisitos', 'caracteristicas'), 'Vacante actualizada con éxito.');
        });
    }

    /**
     * Remove the specified Vacante from storage.
     * DELETE /vacantes/{id}
     */
    public function destroy(Vacante $vacante): JsonResponse
    {
        return $this->executeTransaction(function () use ($vacante) {
            $vacante->addBitacora(
                'Vacante',
                'Vacante eliminada',
                $vacante->generarComentarioEliminacion(),
                'tabler-trash',
                'error'
            );
            $vacante->delete();

            return $this->sendResponse(null, 'Vacante eliminada con éxito.');
        });
    }

    private function sincronizarHijos(Vacante $vacante, array $validados): void
    {
        if (array_key_exists('requisitos', $validados)) {
            $vacante->requisitos()->delete();
            $vacante->requisitos()->createMany($validados['requisitos'] ?? []);
        }

        if (array_key_exists('caracteristicas', $validados)) {
            $vacante->caracteristicas()->delete();
            $vacante->caracteristicas()->createMany($validados['caracteristicas'] ?? []);
        }
    }

    private function completarFechaPublicacion(array $datos, ?Vacante $vacante = null): array
    {
        $codigo = VacanteEstado::whereKey($datos['estado_id'])->value('codigo');
        $fechaActual = $datos['fecha_publicacion'] ?? $vacante?->fecha_publicacion;

        if ($codigo === Vacante::ESTADO_PUBLICADA && empty($fechaActual)) {
            $datos['fecha_publicacion'] = now()->toDateString();
        }

        return $datos;
    }
}
