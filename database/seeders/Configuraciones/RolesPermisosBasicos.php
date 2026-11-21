<?php

namespace Database\Seeders\Configuraciones;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesPermisosBasicos extends Seeder
{

    /**
     * Run the database seeds.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Configuraciones\RolesPermisosBasicos"
     */

    public function run()
    {

        $superAdministrador = Role::firstOrCreate([
            'name' => 'Super Administrador',
            'guard_name' => 'api'
        ]);
        $administrador = Role::firstOrCreate([
            'name' => 'Administrador',
            'guard_name' => 'api'
        ]);
        $dev = Role::firstOrCreate([
            'name' => 'Dev',
            'guard_name' => 'api'
        ]);
        $allEmpleados = Role::firstOrCreate([
            'name' => 'All Empleados',
            'guard_name' => 'api'
        ]);

        // Permisos del módulo Administración/usuarios.
        Permission::firstOrCreate(['name' => 'modulo administracion', 'subject' => 'administracion', 'guard_name'=>'api']);
        //home
        Permission::firstOrCreate(['name' => 'home', 'subject' => 'home', 'guard_name'=>'api']);
        // Usuarios
        Permission::firstOrCreate(['name' => 'modulo usuarios', 'subject' => 'administracion', 'guard_name'=>'api']);
        Permission::firstOrCreate(['name' => 'crear usuarios', 'subject' => 'administracion', 'guard_name'=>'api']);
        Permission::firstOrCreate(['name' => 'editar usuarios', 'subject' => 'administracion', 'guard_name'=>'api']);
        Permission::firstOrCreate(['name' => 'eliminar usuarios', 'subject' => 'administracion', 'guard_name'=>'api']);
        Permission::firstOrCreate(['name' => 'listar usuarios', 'subject' => 'administracion', 'guard_name'=>'api']);
        Permission::firstOrCreate(['name' => 'ver usuarios', 'subject' => 'administracion', 'guard_name'=>'api']);

        // Roles
        Permission::firstOrCreate(['name' => 'listar permisos', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'crear permiso', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'editar permiso', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'ver permiso', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'eliminar permiso', 'subject' => 'administracion', 'guard_name' => 'api']);

        // Permisos
        Permission::firstOrCreate(['name' => 'listar roles', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'crear rol', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'editar rol', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'ver rol', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'eliminar rol', 'subject' => 'administracion', 'guard_name' => 'api']);

        // Menu Opciones
        Permission::firstOrCreate(['name' => 'listar opcion menu', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'crear opcion menu', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'editar opcion menu', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'ver opcion menu','subject' => 'administracion','guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'eliminar opcion menu', 'subject' => 'administracion', 'guard_name' => 'api']);

        // Configuraciones
        Permission::firstOrCreate(['name' => 'listar configuraciones', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'ver configuraciones', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'crear configuraciones', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'editar configuraciones', 'subject' => 'administracion', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'eliminar configuraciones', 'subject' => 'administracion', 'guard_name' => 'api']);

        // Dev
        Permission::firstOrCreate(['name' => 'modulo dev', 'subject' => 'dev', 'guard_name'=>'api']);
        // Ejemplos
        Permission::firstOrCreate(['name' => 'listar ejemplos', 'subject' => 'dev', 'guard_name' => 'api']);
        // Logs
        Permission::firstOrCreate(['name' => 'listar logs', 'subject' => 'dev', 'guard_name' => 'api']);
        // Prueba Apis
        Permission::firstOrCreate(['name' => 'listar prueba Apis', 'subject' => 'dev', 'guard_name' => 'api']);

        // Permisos del módulo Favoritos
        Permission::firstOrCreate(['name' => "ver favoritos", 'subject' => "favoritos", 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => "crear favoritos",'subject' => "favoritos",'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => "editar favoritos", 'subject' => "favoritos",'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => "eliminar favoritos", 'subject' => "favoritos",'guard_name' => 'api']);

        //permisos preferencias de usuario
        Permission::firstOrCreate(['name' => "ver preferencias", 'subject' => "preferencias",'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => "Crear preferencias", 'subject' => "preferencias",'guard_name' => 'api']);

        $superAdministrador->givePermissionTo($this->todosLosPermisosIds());
        $administrador->givePermissionTo($this->todosLosPermisosIds());

        $dev->givePermissionTo($this->todosLosPermisosIds());

        $allEmpleados->givePermissionTo([
            'home',
            'listar opcion menu',
            'ver favoritos',
            'crear favoritos',
            'editar favoritos',
            'eliminar favoritos',
            'ver preferencias',
            'Crear preferencias',
        ]);

        User::whereIn('usuario_dominio', ['gysistema', 'gyjefmolina'])
            ->get()
            ->each(fn(User $user) => $user->syncRoles([$superAdministrador]));
    }

    public function todosLosPermisosIds(): Collection
    {
        return Permission::all()->pluck('id');
    }

}
