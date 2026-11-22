<?php

namespace Database\Seeders\permisos;

use App\Models\Puesto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosReclutamientoPermisosTableSeeder extends Seeder
{

    public function run(): void
    {
        $rolEmpleados = Role::firstOrCreate(['name' => Role::ALL_EMPLEADOS, 'guard_name' => 'api']);
        $rolReclutador = Role::firstOrCreate(['name' => Role::RECLUTADOR, 'guard_name' => 'api']);
        $rolAnalista = Role::firstOrCreate(['name' => Role::ANALISTA, 'guard_name' => 'api']);
        $rolJefeRRHH = Role::firstOrCreate(['name' => Role::JEFE_RRHH, 'guard_name' => 'api']);

        User::query()->each(fn (User $usuario) => $usuario->assignRole($rolEmpleados));

        $permisosBase = [
            'ver postulaciones',
            'ver postulacion estados',
            'seccion postulaciones',
            'editar postulaciones',
            'asignar postulaciones',
            'ver usuarios',
            'ver bitacoras',
            'seccion postulaciones',
        ];

        $permisosReclutador = [
            ...$permisosBase,
            'recibida postulaciones',
            'entrevista postulaciones',
            'oferta postulaciones',
            'crear postulaciones',
            'ver candidatos',
            'crear candidatos',
            'listar postulaciones',

        ];

        $permisosAnalista = [
            ...$permisosBase,
            'revision postulaciones',
            'ver candidatos',
            'listar postulaciones',
        ];

        $permisosJefeRRHH = [
            ...$permisosBase,
            'ver candidatos',
            'crear candidatos',
            'historial candidatos',
            'listar vacante estados',
            'seccion vacantes',
            'listar vacantes',
            'ver vacantes',
            'crear vacantes',
            'ver sedes',
            'ver unidades',
            'editar vacantes',
            'ver vacante estados',
            'crear vacante estados',
            'editar vacante estados',
            'eliminar vacante estados',
            'historial postulaciones',
            'historial candidatos',
            'seccion candidatos',
            'historial candidatos postulaciones',
            'reportes postulaciones',
        ];

        $rolJefeRRHH->givePermissionTo($permisosJefeRRHH);
        $rolReclutador->givePermissionTo($permisosReclutador);
        $rolAnalista->givePermissionTo($permisosAnalista);

        // Asignación inicial de roles según el puesto de los usuarios sembrados.
        User::where('puesto_id', Puesto::RECLUTADOR)->each(fn (User $usuario) => $usuario->assignRole($rolReclutador));
        User::where('puesto_id', Puesto::ANALISTA)->each(fn (User $usuario) => $usuario->assignRole($rolAnalista));
        User::where('puesto_id', Puesto::JEFE_RRHH)->each(fn (User $usuario) => $usuario->assignRole($rolJefeRRHH));

    }
}
