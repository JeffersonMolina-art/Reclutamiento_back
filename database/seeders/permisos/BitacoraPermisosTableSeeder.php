<?php

namespace database\seeders\permisos;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class BitacoraPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'listar bitacoras',
            'ver bitacoras',
            'crear bitacoras',
            'editar bitacoras',
            'eliminar bitacoras',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'Bitacora',
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::where('name', Role::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
