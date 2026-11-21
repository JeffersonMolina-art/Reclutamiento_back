<?php

namespace App\Http\Controllers\Api\Catalogos;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\CreateRegionApiRequest;
use App\Http\Requests\Api\UpdateRegionApiRequest;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;


/**
 * Class RegionApiController
 */
class RegionApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver regiones', only: ['index', 'show']),
            new Middleware('abilities:crear regiones', only: ['store']),
            new Middleware('abilities:editar regiones', only: ['update']),
            new Middleware('abilities:eliminar regiones', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the Regiones.
     * GET|HEAD /regions
     */
    public function index(Request $request): JsonResponse
    {
        return $this->executeOperation(function () {
            $maxResults = (request('page.size') == 0) ? 5 : request('page.size');

            $regiones = QueryBuilder::for(Region::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'codigo',
                    'nombre',
                    'estado',
                    'jefe_id',
                )
                ->allowedFields(
                    'id',
                    'codigo',
                    'nombre',
                    'estado',
                    'jefe_id',
                )
                ->allowedIncludes()
                ->allowedSorts(
                    'codigo',
                    'nombre',
                    'estado',
                    'jefe_id',
                )
                ->jsonPaginate($maxResults); // Paginación spatie

            return $this->sendResponse($regiones, 'Listado de Regiones');
        });
    }


    /**
     * Store a newly created Region in storage.
     * POST /regions
     */
    public function store(CreateRegionApiRequest $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $regions = Region::create($input);

            return $this->sendResponse($regions->toArray(), 'Region creado con éxito.');
        });
    }


    /**
     * Display the specified Region.
     * GET|HEAD /regions/{id}
     */
    public function show(Region $region)
    {
        return $this->executeOperation(function () use ($region) {
            return $this->sendResponse($region->toArray(), 'Region recuperado con éxito.');
        });
    }


    /**
     * Update the specified Region in storage.
     * PUT/PATCH /regions/{id}
     */
    public function update(UpdateRegionApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $region = Region::findOrFail($id);
            $region->update($request->validated());
            return $this->sendResponse($region, 'Region actualizado con éxito.');
        });
    }

    /**
     * Remove the specified Region from storage.
     * DELETE /regions/{id}
     */
    public function destroy(Region $region): JsonResponse
    {
        return $this->executeOperation(function () use ($region) {
            $region->delete();
            return $this->sendResponse(null, 'Region eliminado con éxito.');
        });
    }
}
