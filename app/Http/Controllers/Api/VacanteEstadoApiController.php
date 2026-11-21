<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateVacanteEstadoApiRequest;
use App\Http\Requests\Api\UpdateVacanteEstadoApiRequest;
use App\Models\VacanteEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class VacanteEstadoApiController
 */
class VacanteEstadoApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver vacante estados', only: ['index', 'show']),
            new Middleware('abilities:crear vacante estados', only: ['store']),
            new Middleware('abilities:editar vacante estados', only: ['update']),
            new Middleware('abilities:eliminar vacante estados', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.vacantes_estados.
     * GET|HEAD /vacantes_estados
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $vacantes_estados = QueryBuilder::for(VacanteEstado::class)
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

            return $this->sendResponse($vacantes_estados, 'vacantes_estados recuperados con éxito.');
        });
    }

    /**
     * Store a newly created VacanteEstado in storage.
     * POST /vacantes_estados
     */
    public function store(CreateVacanteEstadoApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $vacantes_estados = VacanteEstado::create($input);

            return $this->sendResponse($vacantes_estados->toArray(), 'VacanteEstado creado con éxito.');
       });
    }

    /**
     * Display the specified VacanteEstado.
     * GET|HEAD /vacantes_estados/{id}
     */
    public function show(VacanteEstado $vacanteestado)
    {
        return $this->executeOperation(function () use ($vacanteestado) {
            $vacanteestado->conAtributoAdicional($request->appends ?? []);
            return $this->sendResponse($vacanteestado->toArray(), 'VacanteEstado recuperado con éxito.');
        });
    }

    /**
    * Update the specified VacanteEstado in storage.
    * PUT/PATCH /vacantes_estados/{id}
    */
    public function update(UpdateVacanteEstadoApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $vacanteestado = VacanteEstado::findOrFail($id);
            $vacanteestado->update($request->validated());
            return $this->sendResponse($vacanteestado, 'VacanteEstado actualizado con éxito.');
        });
    }

    /**
    * Remove the specified VacanteEstado from storage.
    * DELETE /vacantes_estados/{id}
    */
    public function destroy(VacanteEstado $vacanteestado): JsonResponse
    {
        return $this->executeOperation(function () use ($vacanteestado) {
            $vacanteestado->delete();
            return $this->sendResponse(null, 'VacanteEstado eliminado con éxito.');
        });
    }
}
