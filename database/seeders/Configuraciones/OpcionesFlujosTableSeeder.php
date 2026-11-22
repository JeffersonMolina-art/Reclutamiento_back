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
        // VACANTES
        MenuOpcion::updateOrCreate(
            ['titulo_seccion' => 'Vacantes', 'ruta' => null],
            [
                'titulo' => null,
                'icono' => null,
                'action' => 'seccion vacantes',
                'subject' => 'Vacante',
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
// MIS POSTULACIONES
        MenuOpcion::updateOrCreate(
            ['titulo_seccion' => 'Postulaciones', 'ruta' => null],
            [
                'titulo' => null,
                'icono' => null,
                'action' => 'seccion postulaciones',
                'subject' => 'Postulacion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo' => 'Postulaciones'],
            [
                'ruta' => 'postulaciones-asignadas',
                'icono' => 'tabler-briefcase',
                'action' => 'listar postulaciones',
                'subject' => 'Postulacion',
                'titulo_seccion' => null,
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo' => 'Historial de postulaciones'],
            [
                'ruta' => 'postulaciones-historial',
                'icono' => 'tabler-history',
                'action' => 'historial postulaciones',
                'subject' => 'Postulacion',
                'titulo_seccion' => null,
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);
// CANDIDATOS
        MenuOpcion::updateOrCreate(
            ['titulo_seccion' => 'Candidatos', 'ruta' => null],
            [
                'titulo' => null,
                'icono' => null,
                'action' => 'seccion candidatos',
                'subject' => 'Candidato',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo' => 'Candidatos'],
            [
                'ruta' => 'candidatos',
                'icono' => 'tabler-users',
                'action' => 'historial candidatos postulaciones',
                'subject' => 'Candidato',
                'titulo_seccion' => null,
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo' => 'Historial de candidatos'],
            [
                'ruta' => 'candidatos-historial',
                'icono' => 'tabler-timeline',
                'action' => 'historial candidatos',
                'subject' => 'Candidato',
                'titulo_seccion' => null,
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        Permission::firstOrCreate(['name' => 'modulo reclutamiento', 'subject' => 'reclutamiento', 'guard_name' => 'api']);
        Role::whereIn('name', Role::ROLES_ADMINISTRATIVOS)->get()->each->givePermissionTo('modulo reclutamiento');
    }
}
