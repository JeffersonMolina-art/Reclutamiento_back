<?php

namespace App\Http\Controllers\Api\Configuraciones;

use App\Http\Controllers\AppBaseController;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateUserFavoritoApiRequest;
use App\Http\Requests\Api\UpdateUserFavoritoApiRequest;
use App\Models\UserFavorito;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


/**
 * Class UserFavoritoApiController
 */
class UserFavoritoApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver favoritos', only: ['index', 'show']),
            new Middleware('abilities:crear favoritos', only: ['store']),
            new Middleware('abilities:editar favoritos', only: ['update']),
            new Middleware('abilities:eliminar favoritos', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the User_favoritos.
     * GET|HEAD /user-favoritos
     */
    public function index(Request $request): JsonResponse
    {
        $query = UserFavorito::query();

        if ($request->get('skip')) {
            $query->skip($request->get('skip'));
        }

        if ($request->get('limit')) {
            $query->limit($request->get('limit'));
        }

        $userFavoritos = $query->get();

        return $this->sendResponse($userFavoritos->toArray(), 'user-favoritos recuperados con éxito.');
    }

    /**
     * Store a newly created UserFavorito in storage.
     * POST /user-favoritos
     */
    public function store(CreateUserFavoritoApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $userFavoritos = UserFavorito::create($input);

        return $this->sendResponse($userFavoritos->toArray(), 'UserFavorito creado con éxito.');
    }

    /**
     * Display the specified UserFavorito.
     * GET|HEAD /user-favoritos/{id}
     */
    public function show(UserFavorito $userfavorito)
    {
        return $this->sendResponse($userfavorito->toArray(), 'UserFavorito recuperado con éxito.');
    }

    /**
     * Update the specified UserFavorito in storage.
     * PUT/PATCH /user-favoritos/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {

        $user = User::find($id);

        $user->favoritos()->sync($request->favoritos);

        foreach ($request->favoritos as $index => $favoritoId) {
            $user->favoritos()->updateExistingPivot($favoritoId, ['orden' => $index + 1]);
        }

        return $this->sendSuccess('¡Favoritos actualizados exitosamente!');

    }

    /**
     * Remove the specified UserFavorito from storage.
     * DELETE /user-favoritos/{id}
     */
    public function destroy(UserFavorito $userfavorito): JsonResponse
    {
        $userfavorito->delete();
        return $this->sendResponse(null, 'UserFavorito eliminado con éxito.');
    }

    public function obtenerFavoritosDeUser(Request $request)
    {
        $user = $request->user();

        $favoritos = $user->favoritos->sortBy('pivot.orden');

        return $this->sendResponse($favoritos->values()->toArray(), 'Favoritos recuperados con éxito.');

    }

}
