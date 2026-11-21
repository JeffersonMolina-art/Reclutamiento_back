<?php

namespace Database\Seeders\Configuraciones;

use Illuminate\Database\Seeder;

class IndexTableSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Configuraciones\IndexTableSeeder"
     */
    public function run(): void
    {
        $this->call([
            UsuarisistemaSeeder::class,
            OpcionesBasicasTableSeeder::class,
            OpcionesCatalogosTableSeeder::class,
            OpcionesFlujosTableSeeder::class,
            MigrarUsuariosSiguaTableSeeders::class,
            RolesPermisosBasicos::class,
        ]);
    }
}
