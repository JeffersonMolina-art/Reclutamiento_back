<?php

namespace Database\Seeders\Reclutamiento;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostulacionEstadosSeeder extends Seeder
{
    public function run(): void
    {
        $ahora = now();

        $bandejas = [
            'recibidas' => 'Recibidas',
            'en_proceso' => 'En proceso',
            'finalizadas' => 'Finalizadas',
        ];

        foreach ($bandejas as $codigo => $nombre) {
            DB::table('reclutamiento.bandeja')->updateOrInsert(
                ['codigo' => $codigo],
                ['nombre' => $nombre, 'created_at' => $ahora, 'updated_at' => $ahora]
            );
        }

        $bandejaIds = DB::table('reclutamiento.bandeja')->pluck('id', 'codigo');

        $estados = [
            ['recibida', 'Recibida', 'recibidas'],
            ['en_revision', 'En revisión', 'en_proceso'],
            ['entrevista', 'Entrevista', 'en_proceso'],
            ['contratado', 'Contratado', 'finalizadas'],
            ['descartada', 'Descartada', 'finalizadas'],
        ];

        foreach ($estados as [$codigo, $nombre, $bandeja]) {
            DB::table('reclutamiento.postulacion_estado')->updateOrInsert(
                ['codigo' => $codigo],
                [
                    'nombre' => $nombre,
                    'bandeja_id' => $bandejaIds[$bandeja],
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]
            );
        }
    }
}
