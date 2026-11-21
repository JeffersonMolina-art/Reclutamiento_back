<?php

namespace App\Http\Controllers\Api\Configuraciones;


use App\Http\Controllers\AppBaseController;
use App\Http\Requests\JsonApiRequest;
use App\Http\Requests\User\CreateUserPreferenciaRequest;
use App\Http\Resources\User\UserPreferenciaResource;
use App\Http\Resources\User\UserResource;
use App\JsonApi\Exceptions\JsonApiException;
use App\Models\User;
use App\Models\UserPreferencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;
use Spatie\QueryBuilder\QueryBuilder;

class UserPreferenciaController extends AppBaseController implements HasMiddleware
{


    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver preferencias', only: ['index']),
            new Middleware('abilities:Crear preferencias', only: ['store']),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */

    public function index(Request $request)
    {
        $preferencia_id = $request->user()->preferencia ? $request->user()->preferencia->id : null;




        $preferencia = QueryBuilder::for(UserPreferencia::class) // Usar consulta base
        ->allowedFields()   // Campos permitidos
        ->allowedIncludes('')       // Relaciones permitidas
        ->allowedFilters('')   // Filtros permitidos
        ->allowedSorts('')   // Ordenamiento permitido
        ->find($preferencia_id); // Obtener un único elemento




        // Retornar un único recurso
        return $this->sendResponse($preferencia, 'Preferencia actualizada con exitosamente');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if(!$user->preferencia){

            $user->preferencia()->create($request->all());

            return $this->sendResponse($request->all(), 'Preferencia creada con exitosamente');

        } else {

            $user->preferencia->update($request->all());

            return $this->sendResponse(UserPreferenciaResource::make($user), 'Preferencia actualizada con exitosamente');

        }


    }




}
