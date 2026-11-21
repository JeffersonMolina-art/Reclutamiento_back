<?php

namespace Database\Seeders\Reclutamiento;

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
            $existe = DB::table('reclutamiento.vacantes_estados')->where('codigo', $codigo)->exists();

            if ($existe) {
                DB::table('reclutamiento.vacantes_estados')->where('codigo', $codigo)
                    ->update(['nombre' => $nombre, 'updated_at' => now()]);
            } else {
                DB::table('reclutamiento.vacantes_estados')->insert([
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
