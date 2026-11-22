<?php

    namespace Database\Seeders\Reclutamiento;

    use App\Models\Bandeja;
    use App\Models\PostulacionEstado;
    use Illuminate\Database\Seeder;

    class PostulacionEstadosSeeder extends Seeder
    {
        public function run(): void
        {
            $bandejas = [
                'recibidas' => 'Recibidas',
                'en_proceso' => 'En proceso',
                'finalizadas' => 'Finalizadas',
            ];

            $bandejaIds = [];

            foreach ($bandejas as $codigo => $nombre) {
                $bandeja = Bandeja::updateOrCreate(
                    ['codigo' => $codigo],
                    ['nombre' => $nombre]
                );

                $bandejaIds[$codigo] = $bandeja->getKey();
            }

            $estados = [
                ['recibida', 'Recibida', 'recibidas'],
                ['en_revision', 'En revisión', 'en_proceso'],
                ['entrevista', 'Entrevista', 'en_proceso'],
                ['oferta', 'Oferta', 'en_proceso'],
                ['contratado', 'Contratado', 'finalizadas'],
                ['descartada', 'Descartada', 'finalizadas'],
            ];

            foreach ($estados as [$codigo, $nombre, $codigoBandeja]) {
                PostulacionEstado::updateOrCreate(
                    ['codigo' => $codigo],
                    [
                        'nombre' => $nombre,
                        'bandeja_id' => $bandejaIds[$codigoBandeja],
                    ]
                );
            }
        }
    }
