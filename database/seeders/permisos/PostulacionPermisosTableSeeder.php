<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PostulacionPermisosTableSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\permisos\PostulacionPermisosTableSeeder"
     */
    public function run(): void
    {
        $permisos = [
            'listar postulaciones',
            'ver postulaciones',
            'editar postulaciones',
            'asignar postulaciones',
            'seccion postulaciones',
            'recibida postulaciones',
            'revision postulaciones',
            'entrevista postulaciones',
            'oferta postulaciones',
            'crear postulaciones',
            'historial postulaciones',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'Postulacion',
                'guard_name' => 'api',
            ]);
        }

        Role::whereIn('name', Role::ROLES_ADMINISTRATIVOS)->get()->each->givePermissionTo($permisos);
    }
}
