<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Spatie\QueryBuilder\QueryBuilder;

class RoleApiController extends AppBaseController
{
    public function index()
    {
        // Obtener el límite proporcionado desde el frontend (si existe)
        $limite_pagina_front = request('page.size');
        // Limitar el máximo permitido a, por ejemplo, 50 registros por página

        $roles = QueryBuilder::for(Role::class)
            ->allowedFields('id', 'name', 'guard_name', 'created_at', 'updated_at')
            ->allowedIncludes('permissions')
            ->allowedFilters('name', 'guard_name')
            ->allowedSorts('name', 'guard_name', 'created_at', 'updated_at')
            ->jsonPaginate($limite_pagina_front);

        return $this->sendResponse($roles, 'Listado de roles');
    }


    public function store(Request $request)
    {
        try {
            $attributes = $request['data'];
//            $validated = $request->validated();
            DB::beginTransaction();

            $role = Role::create($attributes);

            $this->createAuditPermisos($role, $attributes['permisos']);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Role creado correctamente'
            ], 201);

        }catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function permisos(Role $role)
    {
        return response()->json([
            'data' => $role->permissions
        ]);
    }
    public function show(Role $role)
    {
        return response()->json([
            'data' => $role,
            'permisos' => $role->permissions
        ]);
    }
    public function update(Request $request, Role $role)
    {
        try {
            $attributes = $request['data'];
            DB::beginTransaction();

            $role->update($attributes);
            $role->save();
            $this->updateAuditPermisos($role, $attributes['permisos']);

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Role actualizado correctamente'
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Role $role)
    {
        if (!$role) {
            return response()->json([
                'message' => 'Role no encontrado'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $role->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role eliminado correctamente'
            ], 200);

        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function createAuditPermisos($role, $permisos)
    {
        if ($permisos) {
            foreach ($permisos as $permiso_id) {
                $permiso = Permission::find($permiso_id['id'])->first();
                if ($permiso) {
                    $role->givePermissionTo($permiso);

//                    $assignedPermissions[] = $permiso->name;
                }
            }
        }
    }
    private function updateAuditPermisos($role, $permisos)
    {
        try {

            if ($permisos) {
                $permisos_asignar = [];

                foreach ($permisos as $permiso_id) {
                    $permiso = Permission::find($permiso_id)->first();
                    if ($permiso) {
                        $permisos_asignar[] = $permiso->id;
                    }
                }
                $role->syncPermissions($permisos_asignar);

            } else {
                $role->syncPermissions([]);
            }

        } catch (\Exception $e) {
          throw new \Exception($e->getMessage());
        }
    }

}
