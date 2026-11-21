<?php

namespace Database\Seeders\plantillas;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PlantillaPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'modulo plantillas',
            'listar plantillas',
            'ver plantillas',
            'crear plantillas',
            'editar plantillas',
            'eliminar plantillas',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'Plantilla',
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::where('name', Role::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
