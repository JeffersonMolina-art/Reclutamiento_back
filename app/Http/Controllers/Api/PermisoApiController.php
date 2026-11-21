<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\QueryBuilder\QueryBuilder;

class PermisoApiController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limite_pagina_front = request('page.size');

        $permisos = QueryBuilder::for(Permission::class)
            ->allowedFields('name', 'guard_name', 'created_at', 'updated_at')
            ->allowedIncludes('roles')
            ->allowedFilters('name', 'guard_name')
            ->allowedSorts('name', 'guard_name', 'subject')
            ->jsonPaginate($limite_pagina_front);

        return $this->sendResponse($permisos, 'Listado de permisos');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $attributes = $request['data'];
            $permission = Permission::create($attributes);
            $this->createAuditRoles($permission, $attributes ['roles']);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $permission,
                'message' => 'Permiso creado correctamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Permission $permiso)
    {
        return response()->json([
            'data' => $permiso
        ]);
    }

    public function update(Request $request, Permission $permiso)
    {
        try {
            DB::beginTransaction();
            $attributes = $request['data'];
            $permiso->update($attributes);
            $this->updateAuditRoles($permiso, $attributes ['roles']);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $permiso,
                'message' => 'Permiso actualizado correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Permission $permiso)
    {
        if (!$permiso) {
            return response()->json([
                'message' => 'Permiso no encontrado'
            ], 404);
        }

        try {
            DB::beginTransaction();
            $permiso->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permiso eliminado correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function createAuditRoles($permiso, $roles)
    {
        if ($roles) {
            foreach ($roles as $role_id) {
                $role = Role::find($role_id['id']);
                if ($role) {
                    $role->givePermissionTo($permiso->id);
                }
            }
        }
    }


    private function updateAuditRoles($permiso, $roles)
    {
        try {
            if (is_array($roles)) {
                // Obtener los IDs de los roles seleccionados
                $rolesIdsSeleccionados = array_column($roles, 'id');

                // Obtener todos los roles que tienen actualmente el permiso
                $rolesConPermiso = Role::whereHas('permissions', function ($query) use ($permiso) {
                    $query->where('name', $permiso->name);
                })->pluck('id')->toArray();
                // Roles para los que se debe asignar el permiso
                $rolesParaAsignar = array_diff($rolesIdsSeleccionados, $rolesConPermiso);

                // Roles para los que se debe desasignar el permiso
                $rolesParaRevocar = array_diff($rolesConPermiso, $rolesIdsSeleccionados);
                // Asignar el permiso a los nuevos roles seleccionados
                foreach ($rolesParaAsignar as $roleId) {
                    $role = Role::find($roleId);
                    if ($role) {
                        $role->givePermissionTo($permiso->id);
                    }
                }

                // Revocar el permiso de los roles que ya no están seleccionados
                foreach ($rolesParaRevocar as $roleId) {
                    $role = Role::find($roleId);
                    if ($role) {
                        $role->revokePermissionTo($permiso->id);
                    }
                }

            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function roles(Permission $permiso)
    {
//        return response()->json([
//            'data' => $permiso->roles
//        ]);

        return $this->sendResponse($permiso->roles ?? [], 'Roles asociados al permiso');
    }

    function sujetos(): JsonResponse
    {
        $modelPath = app_path('Models');

        if (!File::exists($modelPath)) {
            return [];
        }

        // Obtener todos los archivos PHP en app/Models
        $files = File::allFiles($modelPath);

        $models = [];
        foreach ($files as $file) {
            $models[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }
        return $this->sendResponse($models, 'Sujetos obtenidos correctamente');
    }
}
