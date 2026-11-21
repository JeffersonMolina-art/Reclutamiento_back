<?php

    namespace Database\Seeders\Reclutamiento;

    use App\Models\Region;
    use Illuminate\Database\Seeder;

    class RegionTableSeeder extends Seeder
    {
        public function run(): void
        {
            $regiones = [
                'Región 1',
                'Región 2',
                'Región 3',
            ];

            foreach ($regiones as $nombre) {
                Region::updateOrCreate(['nombre' => $nombre, 'estado' => 'Activo']);
            }
        }
    }
