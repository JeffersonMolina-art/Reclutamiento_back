<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{

    protected $table = 'reclutamiento.roles';

    const ALL_EMPLEADOS = "All Empleados";
    const ALL_EMPLEADOS_ID = 34;

    const RECLUTADOR = 'Reclutador';
    const ANALISTA = 'Analista de Reclutamiento';

    const ADMIN = "Admin";
    const ADMIN_ID = 78;

    // Roles creados en RolesPermisosBasicos que reciben todos los permisos
    const ROLES_ADMINISTRATIVOS = ['Super Administrador', 'Administrador', 'Dev'];

    public function getPermisosSeparadosComaAttribute(): string
    {
        return $this->permissions->pluck('name')->implode(', ');
    }

}
