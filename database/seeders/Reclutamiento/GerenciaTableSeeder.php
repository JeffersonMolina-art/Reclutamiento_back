<?php

    namespace Database\Seeders\Reclutamiento;

    use App\Models\Gerencia;
    use Illuminate\Database\Seeder;

    class GerenciaTableSeeder extends Seeder
    {
        public function run(): void
        {
            $gerencias = [
                'Gerencia General',
                'Gerencia Financiera',
                'Gerencia de Operaciones',
            ];

            foreach ($gerencias as $nombre) {
                Gerencia::updateOrCreate(['nombre' => $nombre]);
            }
        }
    }
