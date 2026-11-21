<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateGerenciaApiRequest;
use App\Http\Requests\Api\UpdateGerenciaApiRequest;
use App\Models\Gerencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class GerenciaApiController
 */
class GerenciaApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver gerencias', only: ['index', 'show']),
            new Middleware('abilities:crear gerencias', only: ['store']),
            new Middleware('abilities:editar gerencias', only: ['update']),
            new Middleware('abilities:eliminar gerencias', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.gerencias.
     * GET|HEAD /gerencias
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $gerencias = QueryBuilder::for(Gerencia::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'codigo',
                    'nombre',
                    'estado')
                ->allowedSorts(
                    'id',
                    'codigo',
                    'nombre',
                    'estado')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($gerencias, 'gerencias recuperados con éxito.');
        });
    }

    /**
     * Store a newly created Gerencia in storage.
     * POST /gerencias
     */
    public function store(CreateGerenciaApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $gerencias = Gerencia::create($input);

            return $this->sendResponse($gerencias->toArray(), 'Gerencia creado con éxito.');
       });
    }

    /**
     * Display the specified Gerencia.
     * GET|HEAD /gerencias/{id}
     */
    public function show(Gerencia $gerencia)
    {
        return $this->executeOperation(function () use ($gerencia) {
            $gerencia->conAtributoAdicional($request->appends ?? []);
            return $this->sendResponse($gerencia->toArray(), 'Gerencia recuperado con éxito.');
        });
    }

    /**
    * Update the specified Gerencia in storage.
    * PUT/PATCH /gerencias/{id}
    */
    public function update(UpdateGerenciaApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $gerencia = Gerencia::findOrFail($id);
            $gerencia->update($request->validated());
            return $this->sendResponse($gerencia, 'Gerencia actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Gerencia from storage.
    * DELETE /gerencias/{id}
    */
    public function destroy(Gerencia $gerencia): JsonResponse
    {
        return $this->executeOperation(function () use ($gerencia) {
            $gerencia->delete();
            return $this->sendResponse(null, 'Gerencia eliminado con éxito.');
        });
    }
}
