<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\EntrevistaEstado;
use Illuminate\Database\Seeder;

class EntrevistasEstadosSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\EntrevistasEstadosSeeder"
     */
    public function run(): void
    {
        $estados = [
            'programada' => 'Programada',
            'confirmada' => 'Confirmada',
            'reprogramada' => 'Reprogramada',
            'realizada' => 'Realizada',
            'cancelada' => 'Cancelada',
            'no_asistio' => 'No asistió',
        ];

        foreach ($estados as $codigo => $nombre) {
            EntrevistaEstado::updateOrCreate(
                ['codigo' => $codigo],
                ['nombre' => $nombre]
            );
        }
    }
}
