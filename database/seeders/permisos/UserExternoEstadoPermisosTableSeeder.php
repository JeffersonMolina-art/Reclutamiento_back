<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserExternoEstadoPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'listar user externo estados',
            'ver user externo estados',
            'crear user externo estados',
            'editar user externo estados',
            'eliminar user externo estados',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'UserExternoEstado',
                'guard_name' => 'api',
            ]);
        }

        Role::whereIn('name', Role::ROLES_ADMINISTRATIVOS)->get()->each->givePermissionTo($permisos);

    }

}
