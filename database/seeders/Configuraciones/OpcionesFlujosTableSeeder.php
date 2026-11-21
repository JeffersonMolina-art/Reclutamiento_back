<?php

namespace Database\Seeders\Configuraciones;

use App\Models\MenuOpcion;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class OpcionesFlujosTableSeeder extends Seeder
{
    /**
     * Menú de los flujos de reclutamiento. Los permisos de cada flujo se crean en permisos\*.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Configuraciones\OpcionesFlujosTableSeeder"
     */
    public function run(): void
    {
        MenuOpcion::updateOrCreate(
            ['titulo_seccion' => 'Vacantes', 'ruta' => null],
            [
                'titulo' => null,
                'icono' => null,
                'action' => 'sesion vacante',
                'subject' => 'vacante',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo' => 'Vacantes'],
            [
                'ruta' => 'vacante',
                'icono' => 'tabler-briefcase',
                'action' => 'listar vacantes',
                'subject' => 'Vacante',
                'titulo_seccion' => null,
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo_seccion' => 'Postulaciones', 'ruta' => null],
            [
                'titulo' => null,
                'icono' => null,
                'action' => 'listar postulaciones',
                'subject' => 'Postulacion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo' => 'Postulaciones'],
            [
                'ruta' => 'postulaciones-asignadas',
                'icono' => 'tabler-briefcase',
                'action' => 'ver postulaciones',
                'subject' => 'Postulacion',
                'titulo_seccion' => null,
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        Permission::firstOrCreate(['name' => 'modulo reclutamiento', 'subject' => 'reclutamiento', 'guard_name' => 'api']);
        Role::whereIn('name', Role::ROLES_ADMINISTRATIVOS)->get()->each->givePermissionTo('modulo reclutamiento');
    }
}
