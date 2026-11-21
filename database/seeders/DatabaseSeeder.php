<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Configuraciones\MenuOpcionTableSeeder;
use Database\Seeders\Configuraciones\MigrarUsuariosSiguaTableSeeders;
use Database\Seeders\Configuraciones\OpcionesBasicasTableSeeder;
use Database\Seeders\Configuraciones\RolesPermisosBasicos;
use Database\Seeders\Configuraciones\UsuarisistemaSeeder;
use Database\Seeders\Reclutamiento\GerenciasSeeder;
use Database\Seeders\Reclutamiento\PostulacionEstadosSeeder;
use Database\Seeders\Reclutamiento\PuestosSeeder;
use Database\Seeders\Reclutamiento\SedesSeeder;
use Database\Seeders\Reclutamiento\UnidadesSeeder;
use Database\Seeders\Reclutamiento\VacantesEstadosSeeder;
use Database\Seeders\Reclutamiento\VacantesSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\DatabaseSeeder"
     */
    public function run(): void
    {
        $this->call([
            UsuarisistemaSeeder::class,
            OpcionesBasicasTableSeeder::class,
            MigrarUsuariosSiguaTableSeeders::class,
            RolesPermisosBasicos::class,
            GerenciasSeeder::class,
            UnidadesSeeder::class,
            PuestosSeeder::class,
            VacantesEstadosSeeder::class,
            PostulacionEstadosSeeder::class,
            SedesSeeder::class,
            VacantesSeeder::class,
        ]);

    }
}
