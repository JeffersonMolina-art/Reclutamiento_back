<?php

namespace Database\Seeders\Configuraciones;

use App\Models\MenuOpcion;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class OpcionesCatalogosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Configuraciones\OpcionesCatalogosTableSeeder"
     */
    public function run(): void
    {
        $seccion = MenuOpcion::updateOrCreate(
            ['titulo_seccion' => 'Catálogos', 'ruta' => null],
            [
                'titulo' => null,
                'icono' => null,
                'action' => 'modulo catalogos',
                'subject' => 'catalogos',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        $catalogos = MenuOpcion::updateOrCreate(
            ['titulo' => 'Catálogos', 'ruta' => null],
            [
                'icono' => 'tabler-list-details',
                'action' => 'modulo catalogos',
                'subject' => 'catalogos',
                'parent_id' => null,
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        Permission::firstOrCreate(['name' => 'modulo catalogos', 'subject' => 'catalogos', 'guard_name' => 'api']);

        $grupos = [];
        foreach (['Entrevista' => 'tabler-calendar-event', 'Vacante' => 'tabler-briefcase-2'] as $nombre => $icono) {
            $grupos[$nombre] = MenuOpcion::updateOrCreate(
                ['titulo' => $nombre, 'parent_id' => $catalogos->id],
                [
                    'ruta' => null,
                    'icono' => $icono,
                    'action' => 'modulo catalogos',
                    'subject' => 'catalogos',
                    'titulo_seccion' => null,
                    'orden' => MenuOpcion::max('orden') + 1,
                ]);
        }

        // titulo, ruta, icono, nombre del permiso (plural), subject, grupo (opcional)
        $opciones = [
            ['Bandejas', 'catalogos-bandeja', 'tabler-inbox', 'bandejas', 'Bandeja'],
            ['Departamentos', 'catalogos-departamento', 'tabler-map-2', 'departamentos', 'Departamento'],
            ['Estados', 'catalogos-entrevista-estado', 'tabler-calendar-event', 'entrevista estados', 'EntrevistaEstado', 'Entrevista'],
            ['Gerencias', 'catalogos-gerencia', 'tabler-building', 'gerencias', 'Gerencia'],
            ['Municipios', 'catalogos-municipios', 'tabler-map-pin', 'municipios', 'Municipio'],
            ['Países', 'catalogos-pais', 'tabler-world', 'pais', 'Pais'],
            ['Estados de Postulación', 'catalogos-postulacion-estado', 'tabler-file-check', 'postulacion estados', 'PostulacionEstado'],
            ['Puestos', 'catalogos-puesto', 'tabler-briefcase', 'puestos', 'Puesto'],
            ['Regiones', 'catalogos-region', 'tabler-map', 'regiones', 'Region'],
            ['Sedes', 'catalogos-sede', 'tabler-building-community', 'sedes', 'Sede'],
            ['Unidades', 'catalogos-unidad', 'tabler-hierarchy-2', 'unidades', 'Unidad'],
            ['Estados', 'catalogos-vacante-estado', 'tabler-checklist', 'vacante estados', 'VacanteEstado', 'Vacante'],
        ];

        $permisosModulo = ['modulo catalogos'];

        foreach ($opciones as $opcion) {
            [$titulo, $ruta, $icono, $nombre, $subject] = $opcion;
            $padre = isset($opcion[5]) ? $grupos[$opcion[5]] : $catalogos;

            foreach (["listar $nombre", "ver $nombre"] as $permiso) {
                Permission::firstOrCreate([
                    'name' => $permiso,
                    'subject' => $subject,
                    'guard_name' => 'api',
                ]);
                $permisosModulo[] = $permiso;
            }

            MenuOpcion::updateOrCreate(
                ['titulo' => $titulo, 'parent_id' => $padre->id],
                [
                    'ruta' => $ruta,
                    'icono' => $icono,
                    'action' => "listar $nombre",
                    'subject' => $subject,
                    'titulo_seccion' => null,
                    'orden' => MenuOpcion::max('orden') + 1,
                ]);
        }

        Role::whereIn('name', Role::ROLES_ADMINISTRATIVOS)->get()->each->givePermissionTo($permisosModulo);
    }
}
