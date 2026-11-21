<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\UserExternoEstado;
use Illuminate\Database\Seeder;

class UserExternoEstadoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            'Activo',
            'Inactivo',
            'Bloqueado',
        ];

        foreach ($estados as $nombre) {
            UserExternoEstado::updateOrCreate(['nombre' => $nombre, 'codigo' => strtoupper(substr($nombre, 0, 3))]);
        }
    }
}
