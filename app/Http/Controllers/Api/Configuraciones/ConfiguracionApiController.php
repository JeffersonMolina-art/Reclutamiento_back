<?php

namespace App\Http\Controllers\Api\Configuraciones;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateConfiguracionApiRequest;
use App\Http\Requests\Api\UpdateConfiguracionApiRequest;
use App\Models\Configuracion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;


/**
 * Class ConfiguracionApiController
 */
class ConfiguracionApiController extends AppbaseController implements HasMiddleware
{


    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
        new Middleware('abilities:ver configuraciones', only: ['index', 'show']),
        new Middleware('abilities:crear configuraciones', only: ['store']),
        new Middleware('abilities:editar configuraciones', only: ['update']),
        new Middleware('abilities:eliminar configuraciones', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the Configuracions.
     * GET|HEAD /configuracions
     */
    public function index()
    {
        return $this->executeOperation(function () {
            $limite_pagina_default = 2;
            // Obtener el límite proporcionado desde el frontend (si existe)
            $limite_pagina_front = request('page.size');
            // Limitar el máximo permitido a, por ejemplo, 50 registros por página
            $maxResults = ($limite_pagina_front == 0) ? $limite_pagina_default : $limite_pagina_front;

            $configuracion = QueryBuilder::for(Configuracion::class)
                ->allowedFilters('nombre', 'valor','grupo', 'tipo')
                ->allowedFields('nombre', 'valor', 'descripcion', 'tipo', 'grupo', 'cifrado')
                ->allowedIncludes('')       // Relaciones permitidas
                ->allowedSorts('nombre', 'valor', 'tipo', 'grupo')
                ->jsonPaginate($maxResults); // Paginación spatie

            return $this->sendResponse($configuracion, 'Listado de configuraciones');
        });
    }


    /**
     * Store a newly created Configuracion in storage.
     * POST /configuracions
     */
    public function store(CreateConfiguracionApiRequest $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $input = $request->all();

            if (isset($input['cifrado']) && $input['cifrado']) {
                $input['valor'] = cifrar($input['valor']);
            }

            $configuracions = Configuracion::create($input);

            return $this->sendResponse($configuracions->toArray(), 'Configuracion creado con éxito.');
        });
    }


    /**
     * Display the specified Configuracion.
     * GET|HEAD /configuracions/{id}
     */
    public function show(Configuracion $configuracion)
    {
        return $this->executeOperation(function () use ($configuracion) {
            if ($configuracion->cifrado) {
                try {
                    $configuracion->valor = descifrar($configuracion->valor);
                } catch (\Throwable $e) {
                    // Si falla el descifrado, mantener el valor original
                }
            }
            return $this->sendResponse($configuracion->toArray(), 'Configuracion recuperado con éxito.');
        });
    }



    /**
    * Update the specified Configuracion in storage.
    * PUT/PATCH /configuracions/{id}
    */
    public function update(UpdateConfiguracionApiRequest $request, $id): JsonResponse
    {
        return $this->executeOperation(function () use ($request, $id) {
            $configuracion = Configuracion::findOrFail($id);
            $input = $request->validated();

            if (isset($input['cifrado']) && $input['cifrado']) {
                $input['valor'] = cifrar($input['valor']);
            }

            $configuracion->update($input);

            return $this->sendResponse($configuracion, 'Configuracion actualizado con éxito.');
        });
    }

    /**
    * Remove the specified Configuracion from storage.
    * DELETE /configuracions/{id}
    */
    public function destroy(Configuracion $configuracion): JsonResponse
    {
        return $this->executeOperation(function () use ($configuracion) {
            $configuracion->delete();
            return $this->sendResponse(null, 'Configuracion eliminado con éxito.');
        });
    }

    public function storePanelSistema(Request $request)
    {
        try {
            foreach ($request->all() as $key => $value) {

                if($key && $value){

                    $configuracion = Configuracion::updateOrCreate([
                        'nombre' => $key,
                    ],[
                        'nombre' => $key,
                        'valor' => $value,
                        'cifrado' => 0,
                        'descripcion' => 'Panel de sistema',
                        'grupo' => 'panel_sistema',
                        'tipo' => 'string'

                    ]);
                }
            }

            return $this->sendResponse($configuracion, 'Configuraciones del panel de sistema guardadas con éxito.');
        }catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }

    }


    public function obtenerInfoPanel(Request $request)
    {
        $request->validate([
            'grupo' => 'required'
        ]);

        $configuraciones = Configuracion::obtenerConfiguracionesFormateadas($request->grupo);

        return $this->sendResponse($configuraciones, 'Configuraciones del panel de sistema recuperadas con éxito.');

    }

}
