<?php

namespace Database\Seeders;

use Database\Seeders\Configuraciones\IndexTableSeeder as ConfiguracionesIndexTableSeeder;
use Database\Seeders\permisos\IndexTableSeeder as PermisosIndexTableSeeder;
use Database\Seeders\Reclutamiento\IndexTableSeeder as ReclutamientoIndexTableSeeder;
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
            ConfiguracionesIndexTableSeeder::class,
            ReclutamientoIndexTableSeeder::class,
            PermisosIndexTableSeeder::class,
        ]);
    }
}
