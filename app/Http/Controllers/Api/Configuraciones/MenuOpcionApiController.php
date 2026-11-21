<?php

namespace App\Http\Controllers\Api\Configuraciones;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Configuraciones\CreateMenuOpcionRequest;
use App\Http\Resources\User\UserResource;
use App\Models\MenuOpcion;
use App\Models\User;
use CreateMenuOpcionesTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\QueryBuilder;

class MenuOpcionApiController extends AppBaseController implements HasMiddleware
{


    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver opcion menu', only: ['index,show']),
            new Middleware('abilities:crear opcion menu', only: ['store']),
            new Middleware('abilities:editar opcion menu', only: ['update', 'actualizarOrden']),
            new Middleware('abilities:eliminar opcion menu', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortField = $request->get('campoAOrdenar', 'orden');
        $sortDirection = $request->get('ordenCampo', 'asc');
        $tituloFiltro = $request->get('titulo', null);
        $titulo_seccionFiltro = $request->get('titulo_seccion', null);

        $query = MenuOpcion::where('parent_id', null);

        if ($tituloFiltro) {
            $query->where('titulo', 'like', '%' . $tituloFiltro . '%');
        }

        if ($titulo_seccionFiltro) {
            $query->where('titulo_seccion', 'like', '%' . $titulo_seccionFiltro . '%');
        }

        $menuOpciones = $query
            ->orderBy('orden')
            ->with('children')
            ->paginate($request->get('page.size', 100));

        return $this->sendResponse(
            $menuOpciones,
            'Opciones de menú recuperadas con éxito'
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateMenuOpcionRequest $request)
    {

        $ultimaOpcion = MenuOpcion::orderBy('id', 'desc')->first();

        $request->merge(['orden' => $ultimaOpcion ? $ultimaOpcion->orden + 1 : 0]);

        MenuOpcion::create($request->all());

        $menuOpciones = MenuOpcion::where('parent_id', null)
            ->with('children')
            ->orderBy('orden')
            ->get();

        return $this->sendResponse($menuOpciones, 'Opción de menú creada con éxito');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $menuOpcion = MenuOpcion::with('children')->
        find($id);

        return $this->sendResponse($menuOpcion, 'Opción de menú recuperada con éxito');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $menuOpcion = MenuOpcion::find($id);

        $menuOpcion->update($request->all());

        $menuOpciones = MenuOpcion::where('parent_id', null)
            ->with('children')
            ->orderBy('orden')
            ->get();

        return $this->sendResponse($menuOpciones, 'Opción de menú actualizada con éxito');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menuOpcion = MenuOpcion::find($id);

        if (!$menuOpcion) {

            return $this->sendError('Opción de menú no encontrada');
        }

        if ($menuOpcion->children->count() > 0) {
            return $this->sendError('No se puede eliminar una opción de menú que tiene opciones hijas');
        }

        $menuOpcion->delete();

        return $this->sendSuccess('Opción de menú eliminada con éxito');

    }

    /**
     * Actualiza el orden de las opciones de menú
     * @param  Request  $request
     * @return JsonResponse
     */
    public function actualizarOrden(Request $request)
    {

        $opciones = $request->opciones ? $request->opciones : [];

        if (count($opciones) > 0) {

            foreach ($opciones as $key => $opcion) {

                MenuOpcion::find($opcion['id'])->update(['orden' => $key]);

            }
        }

        $menuOpciones = MenuOpcion::where('parent_id', null)
            ->orderBy('orden')
            ->with('children')
            ->get();

        return $this->sendResponse($menuOpciones, 'Opción de menú actualizada con éxito');

    }

}
