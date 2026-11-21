<?php

namespace App\Http\Controllers\Api\Catalogos;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\CreateGerenciaApiRequest;
use App\Http\Requests\Api\UpdateGerenciaApiRequest;
use App\Models\Gerencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


/**
 * Class GerenciaApiController
 */
class GerenciaApiController extends AppbaseController
{


    /**
     * Display a listing of the Gerencias.
     * GET|HEAD /gerencias
     */
    public function index(Request $request): JsonResponse
    {
        $query = Gerencia::query();

        if ($request->get('skip')) {
            $query->skip($request->get('skip'));
        }

        if ($request->get('limit')) {
            $query->limit($request->get('limit'));
        }

        $gerencias = $query->get();

        return $this->sendResponse($gerencias->toArray(), 'gerencias recuperados con éxito.');
    }


    /**
     * Store a newly created Gerencia in storage.
     * POST /gerencias
     */
    public function store(CreateGerenciaApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $gerencias = Gerencia::create($input);

        return $this->sendResponse($gerencias->toArray(), 'Gerencia creado con éxito.');
    }


    /**
     * Display the specified Gerencia.
     * GET|HEAD /gerencias/{id}
     */
    public function show(Gerencia $gerencia)
    {
        return $this->sendResponse($gerencia->toArray(), 'Gerencia recuperado con éxito.');
    }



    /**
    * Update the specified Gerencia in storage.
    * PUT/PATCH /gerencias/{id}
    */
    public function update(UpdateGerenciaApiRequest $request, $id): JsonResponse
    {
        $gerencia = Gerencia::findOrFail($id);
        $gerencia->update($request->validated());
        return $this->sendResponse($gerencia, 'Gerencia actualizado con éxito.');
    }

    /**
    * Remove the specified Gerencia from storage.
    * DELETE /gerencias/{id}
    */
    public function destroy(Gerencia $gerencia): JsonResponse
    {
        $gerencia->delete();
        return $this->sendResponse(null, 'Gerencia eliminado con éxito.');
    }
}
