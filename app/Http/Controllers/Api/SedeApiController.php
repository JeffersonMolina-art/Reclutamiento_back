<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateSedeApiRequest;
use App\Http\Requests\Api\UpdateSedeApiRequest;
use App\Models\Sede;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class SedeApiController
 */
class SedeApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver sedes', only: ['index', 'show']),
            new Middleware('abilities:crear sedes', only: ['store']),
            new Middleware('abilities:editar sedes', only: ['update']),
            new Middleware('abilities:eliminar sedes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.sede.
     * GET|HEAD /sede
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $sede = QueryBuilder::for(Sede::class)
                ->allowedFilters(AllowedFilter::exact('id'), 'nombre')
                ->allowedSorts('nombre')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($sede, 'sede recuperados con éxito.');
        });
    }

    /**
     * Store a newly created Sede in storage.
     * POST /sede
     */
    public function store(CreateSedeApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $sede = Sede::create($input);

            return $this->sendResponse($sede->toArray(), 'Sede creado con éxito.');
       });
    }

    /**
     * Display the specified Sede.
     * GET|HEAD /sede/{id}
     */
    public function show(Sede $sede)
    {
        return $this->executeOperation(function () use ($sede) {
            return $this->sendResponse($sede->toArray(), 'Sede recuperado con éxito.');
        });
    }

    /**
    * Update the specified Sede in storage.
    * PUT/PATCH /sede/{id}
    */
    public function update(UpdateSedeApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $sede = Sede::findOrFail($id);
            $sede->update($request->validated());
            return $this->sendResponse($sede, 'Sede actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Sede from storage.
    * DELETE /sede/{id}
    */
    public function destroy(Sede $sede): JsonResponse
    {
        return $this->executeOperation(function () use ($sede) {
            $sede->delete();
            return $this->sendResponse(null, 'Sede eliminado con éxito.');
        });
    }
}
