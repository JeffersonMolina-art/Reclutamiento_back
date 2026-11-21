<?php

namespace database\seeders\permisos;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class EntrevistaEstadoPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'listar entrevista estados',
            'ver entrevista estados',
            'crear entrevista estados',
            'editar entrevista estados',
            'eliminar entrevista estados',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'EntrevistaEstado',
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::where('name', Role::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
