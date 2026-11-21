<?php

    namespace Database\Seeders\plantillas;

    use Illuminate\Database\Seeder;

    class IndexTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\plantillas\IndexTableSeeder"
         */
        public function run(): void
        {
            $this->call([
                PlantillaPermisosTableSeeder::class,
                PlantillaTipoPermisosTableSeeder::class,
            ]);
        }
    }
