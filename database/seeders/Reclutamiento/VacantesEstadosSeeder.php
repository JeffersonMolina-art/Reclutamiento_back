<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\VacanteEstado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VacantesEstadosSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\VacantesEstadosSeeder"
     */
    public function run(): void
    {
        $estados = [
            'borrador' => 'Borrador',
            'publicada' => 'Publicada',
            'pausada' => 'Pausada',
            'cerrada' => 'Cerrada',
        ];

        foreach ($estados as $codigo => $nombre) {
            VacanteEstado::updateOrCreate(
                ['codigo' => $codigo],
                ['nombre' => $nombre]
            );
        }
    }
}
