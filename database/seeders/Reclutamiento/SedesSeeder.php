<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Sede;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SedesSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\SedesSeeder"
     */
    public function run(): void
    {
        $sedes = [
            'Agencia Jalapa',
            'Agencia Guastatoya',
            'Agencia Sanarate',
            'Agencia Jutiapa',
        ];

        foreach ($sedes as $nombre) {
            Sede::updateOrCreate(['nombre' => $nombre]);
        }
    }
}
