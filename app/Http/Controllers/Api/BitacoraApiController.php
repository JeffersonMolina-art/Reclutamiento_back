<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBitacoraApiRequest;
use App\Http\Requests\Api\UpdateBitacoraApiRequest;
use App\Models\Bitacora;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BitacoraApiController
 */
class BitacoraApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver bitacoras', only: ['index', 'show']),
            new Middleware('abilities:crear bitacoras', only: ['store']),
            new Middleware('abilities:editar bitacoras', only: ['update']),
            new Middleware('abilities:eliminar bitacoras', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.bitacoras.
     * GET|HEAD /bitacoras
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $bitacoras = QueryBuilder::for(Bitacora::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'model_type',
                    AllowedFilter::exact('model_id'),
                    'seccion',
                    'titulo',
                    'comentario',
                    'icono',
                    'color',
                    'usuario_id')
                ->allowedSorts(
                    'id',
                    'model_type',
                    'model_id',
                    'seccion',
                    'titulo',
                    'comentario',
                    'icono',
                    'color',
                    'usuario_id')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($bitacoras, 'bitacoras recuperados con éxito.');
        });
    }

    /**
     * Store a newly created Bitacora in storage.
     * POST /bitacoras
     */
    public function store(CreateBitacoraApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $bitacoras = Bitacora::create($input);

            return $this->sendResponse($bitacoras->toArray(), 'Bitacora creado con éxito.');
       });
    }

    /**
     * Display the specified Bitacora.
     * GET|HEAD /bitacoras/{id}
     */
    public function show(Bitacora $bitacora)
    {
        return $this->executeOperation(function () use ($bitacora) {
            $bitacora->conAtributoAdicional($request->appends ?? []);
            return $this->sendResponse($bitacora->toArray(), 'Bitacora recuperado con éxito.');
        });
    }

    /**
    * Update the specified Bitacora in storage.
    * PUT/PATCH /bitacoras/{id}
    */
    public function update(UpdateBitacoraApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $bitacora = Bitacora::findOrFail($id);
            $bitacora->update($request->validated());
            return $this->sendResponse($bitacora, 'Bitacora actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Bitacora from storage.
    * DELETE /bitacoras/{id}
    */
    public function destroy(Bitacora $bitacora): JsonResponse
    {
        return $this->executeOperation(function () use ($bitacora) {
            $bitacora->delete();
            return $this->sendResponse(null, 'Bitacora eliminado con éxito.');
        });
    }
}
