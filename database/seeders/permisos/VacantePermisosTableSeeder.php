<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class VacantePermisosTableSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\permisos\VacantePermisosTableSeeder"
     */
    public function run(): void
    {
        $permisos = [
            'listar vacantes',
            'ver vacantes',
            'crear vacantes',
            'editar vacantes',
            'eliminar vacantes',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'Vacante',
                'guard_name' => 'api',
            ]);
        }

        Role::whereIn('name', Role::ROLES_ADMINISTRATIVOS)->get()->each->givePermissionTo($permisos);
    }
}
