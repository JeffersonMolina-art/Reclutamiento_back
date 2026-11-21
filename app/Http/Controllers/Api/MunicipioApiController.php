<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateMunicipioApiRequest;
use App\Http\Requests\Api\UpdateMunicipioApiRequest;
use App\Models\Municipio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class MunicipioApiController
 */
class MunicipioApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver municipios', only: ['index', 'show']),
            new Middleware('abilities:crear municipios', only: ['store']),
            new Middleware('abilities:editar municipios', only: ['update']),
            new Middleware('abilities:eliminar municipios', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.municipios.
     * GET|HEAD /municipios
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $municipios = QueryBuilder::for(Municipio::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'codigo',
                    'nombre',
                    'estado',
                    'departamento_id',
                    'existe_agencia')
                ->allowedSorts(
                    'id',
                    'codigo',
                    'nombre',
                    'estado',
                    'departamento_id',
                    'existe_agencia')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($municipios, 'municipios recuperados con éxito.');
        });
    }

    /**
     * Store a newly created Municipio in storage.
     * POST /municipios
     */
    public function store(CreateMunicipioApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $municipios = Municipio::create($input);

            return $this->sendResponse($municipios->toArray(), 'Municipio creado con éxito.');
       });
    }

    /**
     * Display the specified Municipio.
     * GET|HEAD /municipios/{id}
     */
    public function show(Municipio $municipio)
    {
        return $this->executeOperation(function () use ($municipio) {
            $municipio->conAtributoAdicional($request->appends ?? []);
            return $this->sendResponse($municipio->toArray(), 'Municipio recuperado con éxito.');
        });
    }

    /**
    * Update the specified Municipio in storage.
    * PUT/PATCH /municipios/{id}
    */
    public function update(UpdateMunicipioApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $municipio = Municipio::findOrFail($id);
            $municipio->update($request->validated());
            return $this->sendResponse($municipio, 'Municipio actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Municipio from storage.
    * DELETE /municipios/{id}
    */
    public function destroy(Municipio $municipio): JsonResponse
    {
        return $this->executeOperation(function () use ($municipio) {
            $municipio->delete();
            return $this->sendResponse(null, 'Municipio eliminado con éxito.');
        });
    }
}
