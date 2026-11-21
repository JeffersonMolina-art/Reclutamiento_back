<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Pais;
use Illuminate\Database\Seeder;

class PaisTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paises = [
            'Guatemala' => 'Guatemalteca',
        ];

        foreach ($paises as $nombre => $nacionalidad) {
            Pais::updateOrCreate(
                ['nombre' => $nombre],
                ['nacionalidad' => $nacionalidad, 'estado' => 'Activo']
            );
        }
    }
}
