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
            'Guatemala',
        ];

        foreach ($paises as $nombre) {
            Pais::updateOrCreate(['nombre' => $nombre]);
        }
    }
}
