<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateDepartamentoApiRequest;
use App\Http\Requests\Api\UpdateDepartamentoApiRequest;
use App\Models\Departamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class DepartamentoApiController
 */
class DepartamentoApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver departamentos', only: ['index', 'show']),
            new Middleware('abilities:crear departamentos', only: ['store']),
            new Middleware('abilities:editar departamentos', only: ['update']),
            new Middleware('abilities:eliminar departamentos', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.departamentos.
     * GET|HEAD /departamentos
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $departamentos = QueryBuilder::for(Departamento::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'codigo',
                    'nombre',
                    'estado',
                    'codigo_bankworks')
                ->allowedSorts(
                    'id',
                    'codigo',
                    'nombre',
                    'estado',
                    'codigo_bankworks')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($departamentos, 'departamentos recuperados con éxito.');
        });
    }

    /**
     * Store a newly created Departamento in storage.
     * POST /departamentos
     */
    public function store(CreateDepartamentoApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $departamentos = Departamento::create($input);

            return $this->sendResponse($departamentos->toArray(), 'Departamento creado con éxito.');
       });
    }

    /**
     * Display the specified Departamento.
     * GET|HEAD /departamentos/{id}
     */
    public function show(Departamento $departamento)
    {
        return $this->executeOperation(function () use ($departamento) {
            $departamento->conAtributoAdicional($request->appends ?? []);
            return $this->sendResponse($departamento->toArray(), 'Departamento recuperado con éxito.');
        });
    }

    /**
    * Update the specified Departamento in storage.
    * PUT/PATCH /departamentos/{id}
    */
    public function update(UpdateDepartamentoApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $departamento = Departamento::findOrFail($id);
            $departamento->update($request->validated());
            return $this->sendResponse($departamento, 'Departamento actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Departamento from storage.
    * DELETE /departamentos/{id}
    */
    public function destroy(Departamento $departamento): JsonResponse
    {
        return $this->executeOperation(function () use ($departamento) {
            $departamento->delete();
            return $this->sendResponse(null, 'Departamento eliminado con éxito.');
        });
    }
}
