<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreatePostulacionEstadoApiRequest;
use App\Http\Requests\Api\UpdatePostulacionEstadoApiRequest;
use App\Models\PostulacionEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class PostulacionEstadoApiController
 */
class PostulacionEstadoApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver postulacion estados', only: ['index', 'show']),
            new Middleware('abilities:crear postulacion estados', only: ['store']),
            new Middleware('abilities:editar postulacion estados', only: ['update']),
            new Middleware('abilities:eliminar postulacion estados', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.postulacion_estado.
     * GET|HEAD /postulacion_estado
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $postulacion_estado = QueryBuilder::for(PostulacionEstado::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    'nombre',
                    'bandeja_id',
                    'codigo')
                ->allowedSorts(
                    'id',
                    'nombre',
                    'bandeja_id',
                    'codigo')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($postulacion_estado, 'postulacion_estado recuperados con éxito.');
        });
    }

    /**
     * Store a newly created PostulacionEstado in storage.
     * POST /postulacion_estado
     */
    public function store(CreatePostulacionEstadoApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $postulacion_estado = PostulacionEstado::create($input);

            return $this->sendResponse($postulacion_estado->toArray(), 'PostulacionEstado creado con éxito.');
       });
    }

    /**
     * Display the specified PostulacionEstado.
     * GET|HEAD /postulacion_estado/{id}
     */
    public function show(PostulacionEstado $postulacionestado)
    {
        return $this->executeOperation(function () use ($postulacionestado) {
            $postulacionestado->conAtributoAdicional($request->appends ?? []);
            return $this->sendResponse($postulacionestado->toArray(), 'PostulacionEstado recuperado con éxito.');
        });
    }

    /**
    * Update the specified PostulacionEstado in storage.
    * PUT/PATCH /postulacion_estado/{id}
    */
    public function update(UpdatePostulacionEstadoApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $postulacionestado = PostulacionEstado::findOrFail($id);
            $postulacionestado->update($request->validated());
            return $this->sendResponse($postulacionestado, 'PostulacionEstado actualizado con éxito.');
        });
    }

    /**
    * Remove the specified PostulacionEstado from storage.
    * DELETE /postulacion_estado/{id}
    */
    public function destroy(PostulacionEstado $postulacionestado): JsonResponse
    {
        return $this->executeOperation(function () use ($postulacionestado) {
            $postulacionestado->delete();
            return $this->sendResponse(null, 'PostulacionEstado eliminado con éxito.');
        });
    }
}
