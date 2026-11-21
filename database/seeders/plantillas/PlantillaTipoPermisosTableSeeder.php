<?php

namespace Database\Seeders\plantillas;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PlantillaTipoPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'listar plantilla tipos',
            'ver plantilla tipos',
            'crear plantilla tipos',
            'editar plantilla tipos',
            'eliminar plantilla tipos',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'PlantillaTipo',
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::where('name', Role::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
