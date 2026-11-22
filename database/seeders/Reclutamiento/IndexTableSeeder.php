<?php

namespace Database\Seeders\Reclutamiento;

use Illuminate\Database\Seeder;

class IndexTableSeeder extends Seeder
{
    /**
     * Requiere: UsuarisistemaSeeder (VacantesSeeder usa al usuario gyjefmolina).
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\IndexTableSeeder"
     */
    public function run(): void
    {
        $this->call([
            PaisTableSeeder::class,
            RegionTableSeeder::class,
            GerenciaTableSeeder::class,
            UnidadesSeeder::class,
            PuestoTableSeeder::class,
            UsuariosReclutamientoSeeder::class,
            UserExternoEstadoTableSeeder::class,
            UserExternoSeeder::class,
            VacantesEstadosSeeder::class,
            EntrevistasEstadosSeeder::class,
            PostulacionEstadosSeeder::class,
            SedesSeeder::class,
            VacantesSeeder::class,
            DepartamentoTableSeeder::class,
            MunicipioTableSeeder::class,
        ]);
    }
}
