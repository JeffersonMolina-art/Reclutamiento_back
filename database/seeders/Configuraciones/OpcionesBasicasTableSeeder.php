<?php

namespace Database\Seeders\Configuraciones;

use App\Models\MenuOpcion;
use Illuminate\Database\Seeder;

class OpcionesBasicasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Configuraciones\OpcionesBasicasTableSeeder"
     */
    public function run(): void
    {
        $menuOpciones = [];
        MenuOpcion::updateOrCreate(
            ['titulo' => 'Inicio', 'ruta' => 'index'],
            [
                'icono' => 'tabler-home',
                'action' => 'home',
                'subject' => 'home',
                'parent_id' => null,
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);
        $menuOpciones['tituloSeccion']  = MenuOpcion::updateOrCreate(
            ['titulo_seccion' => 'Administración', 'ruta' => null],
            [
                'titulo' => null,
                'icono' => null,
                'action' => 'modulo administracion',
                'subject' => 'administracion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        $menuOpciones['Administracion'] = MenuOpcion::updateOrCreate(
            ['titulo' => 'Administración',  'ruta' => null],
            [
                'icono' => 'tabler-users-group',
                'action' => 'modulo administracion',
                'subject' => 'administracion',
                'parent_id' => null,
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        $menuOpciones['Usuarios'] = MenuOpcion::updateOrCreate(
            ['titulo' => 'Usuarios', 'parent_id' => $menuOpciones['Administracion']->id],
            [
                'ruta' => null,
                'icono' => 'tabler-users',
                'action' => 'modulo usuarios',
                'subject' => 'administracion',
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo' => 'Listar Usuarios', 'parent_id' => $menuOpciones['Usuarios']->id,],
            [
                'ruta' => 'admin-usuarios',
                'icono' => 'tabler-user',
                'action' => 'listar usuarios',
                'subject' => 'administracion',
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);
        MenuOpcion::updateOrCreate(
            ['titulo' => 'Roles', 'parent_id' => $menuOpciones['Usuarios']->id,],
            [
                'ruta' => 'admin-usuarios-roles',
                'icono' => 'tabler-user-check',
                'action' => 'listar roles',
                'subject' => 'administracion',
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);
        MenuOpcion::updateOrCreate(
            ['titulo' => 'Permisos', 'parent_id' => $menuOpciones['Usuarios']->id,],
            [
                'ruta' => 'admin-usuarios-permisos',
                'icono' => 'tabler-shield-pin',
                'action' => 'listar permisos',
                'subject' => 'administracion',
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);
        MenuOpcion::updateOrCreate(
            ['titulo' => 'Menu', 'parent_id' => $menuOpciones['Administracion']->id,],
            [
                'ruta' => 'admin-menu',
                'icono' => 'tabler-apps',
                'action' => 'listar opcion menu',
                'subject' => 'administracion',
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);
        MenuOpcion::updateOrCreate(
            ['titulo' => 'Configuraciones', 'parent_id' => $menuOpciones['Administracion']->id,],
            [
                'ruta' => 'admin-configuraciones',
                'icono' => 'tabler-settings',
                'action' => 'listar configuraciones',
                'subject' => 'administracion',
                'titulo_seccion' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);

        MenuOpcion::updateOrCreate(
            ['titulo_seccion' => 'Desarrollo', 'ruta' => null],
            [
                'titulo' => null,
                'ruta' => null,
                'icono' => null,
                'action' => 'modulo dev',
                'subject' => 'dev',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]);
        $menuOpciones['Dev'] =MenuOpcion::updateOrCreate(
            ['titulo' => 'Developer', 'ruta' => null],
            [
                'ruta' => null,
                'icono' => 'tabler-brand-nuxt',
                'action' => 'modulo dev',
                'subject' => 'dev',
                'titulo_seccion' => null,
                'parent_id' => null,
                'orden' =>  MenuOpcion::max('orden') + 1,
            ]);
        MenuOpcion::updateOrCreate(
            ['titulo' => 'Ejemplos', 'parent_id' => $menuOpciones['Dev']->id,],
            [
                'ruta' => 'dev-ejemplos',
                'icono' => 'tabler-tools',
                'action' => 'listar ejemplos',
                'subject' => 'dev',
                'titulo_seccion' => null,
                'orden' =>  MenuOpcion::max('orden') + 1,
            ]);
        MenuOpcion::updateOrCreate(
            ['titulo' => 'Logs', 'parent_id' => $menuOpciones['Dev']->id,],
            [
                'ruta' => 'dev-log',
                'icono' => 'tabler-file-text',
                'action' => 'listar logs',
                'subject' => 'dev',
                'titulo_seccion' => null,
                'orden' =>  MenuOpcion::max('orden') + 1,
            ]);
        MenuOpcion::updateOrCreate(
            ['titulo' => 'Prueba Apis', 'parent_id' => $menuOpciones['Dev']->id,],
            [
                'ruta' => 'dev-pruebaApis',
                'icono' => 'tabler-api',
                'action' => 'listar prueba Apis',
                'subject' => 'dev',
                'titulo_seccion' => null,
                'orden' =>  MenuOpcion::max('orden') + 1,
            ]);
    }
}




