<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CandidatoPermisosTableSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\permisos\CandidatoPermisosTableSeeder"
     */
    public function run(): void
    {
        $permisos = [
            'ver candidatos',
            'crear candidatos',
            'historial candidatos',
            'seccion candidatos',
            'historial candidatos postulaciones',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'Candidato',
                'guard_name' => 'api',
            ]);
        }

        Role::whereIn('name', Role::ROLES_ADMINISTRATIVOS)->get()->each->givePermissionTo($permisos);
    }
}
