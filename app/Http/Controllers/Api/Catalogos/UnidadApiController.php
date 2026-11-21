<?php

namespace App\Http\Controllers\Api\Catalogos;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\CreateUnidadApiRequest;
use App\Http\Requests\Api\UpdateUnidadApiRequest;
use App\Models\Unidad;
use App\QueryFilters\Unidad\UnidadUsuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;


/**
 * Class UnidadApiController
 */
class UnidadApiController extends AppbaseController
{


    /**
     * Display a listing of the Unidads.
     * GET|HEAD /unidads
     */
    public function index(Request $request): JsonResponse
    {
        return $this->executeOperation(function () {
            $maxResults = (request('page.size') == 0) ? 5 : request('page.size');

            $unidads = QueryBuilder::for(Unidad::class)
                ->allowedFilters(
                    'codigo',
                    'nombre',
                    'emails',
                    'estado',
                    'regiones_id',
                    'gerencia_id',
                    'jefe_id',
                    'auxiliar_id',
                    'auxiliar2_id',
                    'email',
                    'municipio_id',
                    'departamento_id',
                    'latitud',
                    'longitud',
                    'id_place_google',
                    'codigo_aprobador',
                    'grupo_aprobador',
                    'codigo_area_t24',
                )
                ->allowedIncludes()
                ->allowedSorts(
                    'id',
                    'codigo',
                    'nombre',
                    'emails',
                    'estado',
                    'regiones_id',
                    'gerencia_id',
                    'jefe_id',
                    'auxiliar_id',
                    'auxiliar2_id',
                    'email',
                    'municipio_id',
                    'departamento_id',
                    'latitud',
                    'longitud',
                    'id_place_google',
                    'codigo_aprobador',
                    'grupo_aprobador',
                    'codigo_area_t24',
                )
                ->jsonPaginate($maxResults);

            return $this->sendResponse($unidads->toArray(), 'unidads recuperados con éxito.');
        });
    }


    /**
     * Store a newly created Unidad in storage.
     * POST /unidads
     */
    public function store(CreateUnidadApiRequest $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $unidads = Unidad::create($input);

            return $this->sendResponse($unidads->toArray(), 'Unidad creado con éxito.');
        });
    }


    /**
     * Display the specified Unidad.
     * GET|HEAD /unidads/{id}
     */
    public function show(Unidad $unidad)
    {
        return $this->executeOperation(function () use ($unidad) {
            return $this->sendResponse($unidad->toArray(), 'Unidad recuperado con éxito.');
        });
    }


    /**
     * Update the specified Unidad in storage.
     * PUT/PATCH /unidads/{id}
     */
    public function update(UpdateUnidadApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $unidad = Unidad::findOrFail($id);
            $unidad->update($request->validated());
            return $this->sendResponse($unidad, 'Unidad actualizado con éxito.');
        });
    }

    /**
     * Remove the specified Unidad from storage.
     * DELETE /unidads/{id}
     */
    public function destroy(Unidad $unidad): JsonResponse
    {
        return $this->executeOperation(function () use ($unidad) {
            $unidad->delete();
            return $this->sendResponse(null, 'Unidad eliminado con éxito.');
        });
    }
}
