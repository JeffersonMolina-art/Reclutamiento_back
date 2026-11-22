<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreatePaisApiRequest;
use App\Http\Requests\Api\UpdatePaisApiRequest;
use App\Models\Pais;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class PaisApiController
 */
class PaisApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver pais', only: ['index', 'show']),
            new Middleware('abilities:crear pais', only: ['store']),
            new Middleware('abilities:editar pais', only: ['update']),
            new Middleware('abilities:eliminar pais', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.pais.
     * GET|HEAD /pais
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $pais = QueryBuilder::for(Pais::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'codigo',
                    'nombre',
                    'nacionalidad',
                    'estado')
                ->allowedSorts(
                    'id',
                    'codigo',
                    'nombre',
                    'nacionalidad',
                    'estado')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($pais, 'pais recuperados con éxito.');
        });
    }

    /**
     * Store a newly created Pais in storage.
     * POST /pais
     */
    public function store(CreatePaisApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $pais = Pais::create($input);

            return $this->sendResponse($pais->toArray(), 'Pais creado con éxito.');
       });
    }

    /**
     * Display the specified Pais.
     * GET|HEAD /pais/{id}
     */
    public function show(Pais $pais)
    {
        return $this->executeOperation(function () use ($pais) {
            return $this->sendResponse($pais->toArray(), 'Pais recuperado con éxito.');
        });
    }

    /**
    * Update the specified Pais in storage.
    * PUT/PATCH /pais/{id}
    */
    public function update(UpdatePaisApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $pais = Pais::findOrFail($id);
            $pais->update($request->validated());
            return $this->sendResponse($pais, 'Pais actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Pais from storage.
    * DELETE /pais/{id}
    */
    public function destroy(Pais $pais): JsonResponse
    {
        return $this->executeOperation(function () use ($pais) {
            $pais->delete();
            return $this->sendResponse(null, 'Pais eliminado con éxito.');
        });
    }
}
