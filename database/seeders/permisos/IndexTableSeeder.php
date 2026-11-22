<?php

namespace Database\Seeders\permisos;

use Illuminate\Database\Seeder;

class IndexTableSeeder extends Seeder
{
    /**
     * Requiere el rol admin creado en Configuraciones\RolesPermisosBasicos.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\permisos\IndexTableSeeder"
     */
    public function run(): void
    {
        $this->call([
            BandejaPermisosTableSeeder::class,
            BitacoraPermisosTableSeeder::class,
            DepartamentoPermisosTableSeeder::class,
            EntrevistaEstadoPermisosTableSeeder::class,
            GerenciaPermisosTableSeeder::class,
            MunicipioPermisosTableSeeder::class,
            PaisPermisosTableSeeder::class,
            PostulacionEstadoPermisosTableSeeder::class,
            PuestoPermisosTableSeeder::class,
            RegionPermisosTableSeeder::class,
            SedePermisosTableSeeder::class,
            UnidadPermisosTableSeeder::class,
            UserExternoEstadoPermisosTableSeeder::class,
            VacantePermisosTableSeeder::class,
            VacanteEstadoPermisosTableSeeder::class,
            PostulacionPermisosTableSeeder::class,
            CandidatoPermisosTableSeeder::class,
            UsuariosReclutamientoPermisosTableSeeder::class,
        ]);
    }
}
