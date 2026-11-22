<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBandejaApiRequest;
use App\Http\Requests\Api\UpdateBandejaApiRequest;
use App\Models\Bandeja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BandejaApiController
 */
class BandejaApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver bandejas', only: ['index', 'show']),
            new Middleware('abilities:crear bandejas', only: ['store']),
            new Middleware('abilities:editar bandejas', only: ['update']),
            new Middleware('abilities:eliminar bandejas', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.bandeja.
     * GET|HEAD /bandeja
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $bandeja = QueryBuilder::for(Bandeja::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'nombre',
                    'codigo'
                )
                ->allowedSorts(
                    'id',
                    'nombre',
                    'codigo'
                )
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($bandeja, 'bandeja recuperados con éxito.');
        });
    }

    /**
     * Store a newly created Bandeja in storage.
     * POST /bandeja
     */
    public function store(CreateBandejaApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $bandeja = Bandeja::create($input);

            return $this->sendResponse($bandeja->toArray(), 'Bandeja creado con éxito.');
       });
    }

    /**
     * Display the specified Bandeja.
     * GET|HEAD /bandeja/{id}
     */
    public function show(Bandeja $bandeja)
    {
        return $this->executeOperation(function () use ($bandeja) {
            return $this->sendResponse($bandeja->toArray(), 'Bandeja recuperado con éxito.');
        });
    }

    /**
    * Update the specified Bandeja in storage.
    * PUT/PATCH /bandeja/{id}
    */
    public function update(UpdateBandejaApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $bandeja = Bandeja::findOrFail($id);
            $bandeja->update($request->validated());
            return $this->sendResponse($bandeja, 'Bandeja actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Bandeja from storage.
    * DELETE /bandeja/{id}
    */
    public function destroy(Bandeja $bandeja): JsonResponse
    {
        return $this->executeOperation(function () use ($bandeja) {
            $bandeja->delete();
            return $this->sendResponse(null, 'Bandeja eliminado con éxito.');
        });
    }
}
