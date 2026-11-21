<?php

namespace Database\Seeders\Reclutamiento;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SedesSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\SedesSeeder"
     */
    public function run(): void
    {
        foreach (['Casa Matriz', 'Agencia Zona 1', 'Agencia Zona 10', 'Agencia Quetzaltenango'] as $nombre) {
            if (!DB::table('reclutamiento.sede')->where('nombre', $nombre)->exists()) {
                DB::table('reclutamiento.sede')->insert([
                    'nombre' => $nombre,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
