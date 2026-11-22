<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateEntrevistaEstadoApiRequest;
use App\Http\Requests\Api\UpdateEntrevistaEstadoApiRequest;
use App\Models\EntrevistaEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class EntrevistaEstadoApiController
 */
class EntrevistaEstadoApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver entrevista estados', only: ['index', 'show']),
            new Middleware('abilities:crear entrevista estados', only: ['store']),
            new Middleware('abilities:editar entrevista estados', only: ['update']),
            new Middleware('abilities:eliminar entrevista estados', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.entrevistas_estado.
     * GET|HEAD /entrevistas_estado
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $entrevistas_estado = QueryBuilder::for(EntrevistaEstado::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'nombre',
                    'codigo')
                ->allowedSorts(
                    'id',
                    'nombre',
                    'codigo')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($entrevistas_estado, 'entrevistas_estado recuperados con éxito.');
        });
    }

    /**
     * Store a newly created EntrevistaEstado in storage.
     * POST /entrevistas_estado
     */
    public function store(CreateEntrevistaEstadoApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $entrevistas_estado = EntrevistaEstado::create($input);

            return $this->sendResponse($entrevistas_estado->toArray(), 'EntrevistaEstado creado con éxito.');
       });
    }

    /**
     * Display the specified EntrevistaEstado.
     * GET|HEAD /entrevistas_estado/{id}
     */
    public function show(EntrevistaEstado $entrevistaestado)
    {
        return $this->executeOperation(function () use ($entrevistaestado) {
            return $this->sendResponse($entrevistaestado->toArray(), 'EntrevistaEstado recuperado con éxito.');
        });
    }

    /**
    * Update the specified EntrevistaEstado in storage.
    * PUT/PATCH /entrevistas_estado/{id}
    */
    public function update(UpdateEntrevistaEstadoApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $entrevistaestado = EntrevistaEstado::findOrFail($id);
            $entrevistaestado->update($request->validated());
            return $this->sendResponse($entrevistaestado, 'EntrevistaEstado actualizado con éxito.');
        });
    }

    /**
    * Remove the specified EntrevistaEstado from storage.
    * DELETE /entrevistas_estado/{id}
    */
    public function destroy(EntrevistaEstado $entrevistaestado): JsonResponse
    {
        return $this->executeOperation(function () use ($entrevistaestado) {
            $entrevistaestado->delete();
            return $this->sendResponse(null, 'EntrevistaEstado eliminado con éxito.');
        });
    }
}
