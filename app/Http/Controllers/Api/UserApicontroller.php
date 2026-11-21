<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\CreateUserApiRequest;
use App\Http\Requests\Api\UpdateUserApiRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Responses\TokenResponse;
use App\Models\User;
use App\QueryFilters\Usuario\UsuariosPorRegionFilter;
use App\QueryFilters\Usuario\UsuariosPorUnidadFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserApicontroller extends AppBaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver usuarios', only: ['index', 'show']),
            new Middleware('abilities:crear usuarios', only: ['store']),
            new Middleware('abilities:editar usuarios', only: ['update']),
            new Middleware('abilities:eliminar usuarios', only: ['destroy']),
            new Middleware('abilities:restablecer contraseña usuario', only: ['restablecerContrasena']),
            new Middleware('abilities:iniciar sesión como otro usuario', only: ['devLoginAsUser']),
        ];
    }

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        return $this->executeOperation(function () {
            /**
             * @var $user Builder
             */
            $user = QueryBuilder::for(User::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    AllowedFilter::scope('buscarPorNombre', 'buscarPorNombre'),
                    'usuario_dominio',
                    'email',
                    'nombre',
                    'apellido',
                    'estado',
                    AllowedFilter::exact('roles.id', 'roles.id'),
                    AllowedFilter::exact('puesto_id'),
                    AllowedFilter::exact('region_id'),
                    AllowedFilter::exact('unidad_id'),
                    AllowedFilter::exact('gerencia_id'),
                )
                ->allowedFields(
                    'id',
                    'usuario_dominio',
                    'email',
                    'nombre',
                    'apellido'
                )
                ->allowedIncludes(
                    'gerencia',
                    'puesto',
                    'unidad',
                    'region'
                )
                ->allowedSorts(
                    'usuario_dominio',
                    'email',
                    'id',
                    'nombre',
                    'apellido',
                    'created_at'
                )
                ->jsonPaginate(request('page.size', 10));


            return $this->sendResponse($user, 'Listado de usuarios');
        });
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserApiRequest $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $data = $request->all();

            $ultimoUser = User::latest('id')->first();

            $data['id'] = $ultimoUser->id + 1;

            $data['password'] = bcrypt('Gy202500');

            $user = User::create($data);

            return $this->sendResponse(UserResource::make($user), 'Usuario creado con éxito');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->executeOperation(function () use ($user) {
            $user->load(
                'roles:id,name',
                'permissions:id,name',
                'gerencia:id,nombre',
                'puesto:id,nombre',
                'unidad:id,nombre',
                'region:id,nombre'
            );
            return $this->sendResponse($user, 'Usuario recuperado con éxito');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserApiRequest $request, User $user)
    {
        return $this->executeTransaction(function () use ($request, $user) {
            $user->update($request->all());

            if ($request->filled('roles')) {
                $user->syncRoles($request->roles);
            }

            if ($request->filled('permissions')) {
                $user->syncPermissions($request->permissions);
            }

            return $this->sendResponse(UserResource::make($user), 'Usuario actualizado con éxito');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        return $this->executeOperation(function () use ($user) {
            $user->delete();

            return $this->sendResponse([], 'Usuario eliminado con éxito');
        });
    }

    /**
     * Restablece la contraseña del usuario a un valor predeterminado.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function restablecerContrasena(User $user)
    {
        return $this->executeOperation(function () use ($user) {
            $newPassword = bcrypt('Gy' . now()->year . '00');

            $user->password = $newPassword;
            $user->save();

            return $this->sendResponse([], 'Contraseña restablecida con éxito');
        });
    }

    public function devLoginAsUser(Request $request)
    {
        if (!app()->environment('local')) {
            abort(403, 'Funcionalidad no permitida fuera de entorno local');
        }

        try {
            $user = User::findOrFail($request->user_id);
            return new TokenResponse(user: $user);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al intentar iniciar sesión con el usuario seleccionado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function actualizarFotoPerfil(Request $request): JsonResponse
    {
        return $this->executeTransaction(function () use ($request) {
            $request->validate([
                'nueva_foto_perfil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'user_id'     => 'required|exists:sqlsrv.reclutamiento.users,id',
            ]);

            $user = User::findOrFail($request->user_id);
            $fotoPerfil = $request->file('nueva_foto_perfil');

            $user->addMedia($fotoPerfil)
                ->toMediaCollection('foto_perfil');

            return $this->sendSuccess('Foto de perfil actualizada con éxito');
        });
    }


}
