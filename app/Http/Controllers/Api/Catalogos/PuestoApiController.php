<?php

namespace App\Http\Controllers\Api\Catalogos;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\CreatePuestoApiRequest;
use App\Http\Requests\Api\UpdatePuestoApiRequest;
use App\Models\Puesto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;


/**
 * Class PuestoApiController
 */
class PuestoApiController extends AppbaseController
{


    /**
     * Display a listing of the Puestos.
     * GET|HEAD /puestos
     */
    public function index(Request $request): JsonResponse
    {
        return $this->executeOperation(function () {
            $puestos = QueryBuilder::for(Puesto::class)
                ->allowedFilters(
                    'id',
                    'nombre',
                    'gerencia_id',
                    'productividad',
                    'estado'
                )
                ->allowedSorts(
                    'id',
                    'nombre',
                    'gerencia_id',
                    'productividad',
                    'estado'
                )
                ->defaultSort('id')
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($puestos->toArray(), 'puestos recuperados con éxito.');
        });
    }


    /**
     * Store a newly created Puesto in storage.
     * POST /puestos
     */
    public function store(CreatePuestoApiRequest $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $puestos = Puesto::create($input);

            return $this->sendResponse($puestos->toArray(), 'Puesto creado con éxito.');
        });
    }


    /**
     * Display the specified Puesto.
     * GET|HEAD /puestos/{id}
     */
    public function show(Puesto $puesto)
    {
        return $this->executeOperation(function () use ($puesto) {
            return $this->sendResponse($puesto->toArray(), 'Puesto recuperado con éxito.');
        });
    }



    /**
    * Update the specified Puesto in storage.
    * PUT/PATCH /puestos/{id}
    */
    public function update(UpdatePuestoApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $puesto = Puesto::findOrFail($id);
            $puesto->update($request->validated());
            return $this->sendResponse($puesto, 'Puesto actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Puesto from storage.
    * DELETE /puestos/{id}
    */
    public function destroy(Puesto $puesto): JsonResponse
    {
        return $this->executeOperation(function () use ($puesto) {
            $puesto->delete();
            return $this->sendResponse(null, 'Puesto eliminado con éxito.');
        });
    }
}
