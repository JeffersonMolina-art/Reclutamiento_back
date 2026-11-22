<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateUserExternoEstadoApiRequest;
use App\Http\Requests\Api\UpdateUserExternoEstadoApiRequest;
use App\Models\UserExternoEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class UserExternoEstadoApiController
 */
class UserExternoEstadoApiController extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver user externo estados', only: ['index', 'show']),
            new Middleware('abilities:crear user externo estados', only: ['store']),
            new Middleware('abilities:editar user externo estados', only: ['update']),
            new Middleware('abilities:eliminar user externo estados', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Reclutamiento.usuario_externo_estados.
     * GET|HEAD /usuario_externo_estados
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $usuario_externo_estados = QueryBuilder::for(UserExternoEstado::class)
                ->allowedFilters('nombre',
                    'codigo')
                ->allowedSorts('nombre',
                    'codigo')
                ->defaultSort('-id') // Ordenar por defecto por fecha descendente
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($usuario_externo_estados, 'usuario_externo_estados recuperados con éxito.');
        });
    }

    /**
     * Store a newly created UserExternoEstado in storage.
     * POST /usuario_externo_estados
     */
    public function store(CreateUserExternoEstadoApiRequest $request): JsonResponse
    {
       return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            $usuario_externo_estados = UserExternoEstado::create($input);

            return $this->sendResponse($usuario_externo_estados->toArray(), 'UserExternoEstado creado con éxito.');
       });
    }

    /**
     * Display the specified UserExternoEstado.
     * GET|HEAD /usuario_externo_estados/{id}
     */
    public function show(UserExternoEstado $userexternoestado)
    {
        return $this->executeOperation(function () use ($userexternoestado) {
            return $this->sendResponse($userexternoestado->toArray(), 'UserExternoEstado recuperado con éxito.');
        });
    }

    /**
    * Update the specified UserExternoEstado in storage.
    * PUT/PATCH /usuario_externo_estados/{id}
    */
    public function update(UpdateUserExternoEstadoApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $userexternoestado = UserExternoEstado::findOrFail($id);
            $userexternoestado->update($request->validated());
            return $this->sendResponse($userexternoestado, 'UserExternoEstado actualizado con éxito.');
        });
    }

    /**
    * Remove the specified UserExternoEstado from storage.
    * DELETE /usuario_externo_estados/{id}
    */
    public function destroy(UserExternoEstado $userexternoestado): JsonResponse
    {
        return $this->executeOperation(function () use ($userexternoestado) {
            $userexternoestado->delete();
            return $this->sendResponse(null, 'UserExternoEstado eliminado con éxito.');
        });
    }
}
