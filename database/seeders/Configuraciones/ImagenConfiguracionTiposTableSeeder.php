<?php

namespace Database\Seeders\configuraciones;

use App\Models\ImagenConfiguracionTipo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagenConfiguracionTiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejectuar este seeder: php artisan db:seed --class="Database\Seeders\configuraciones\ImagenConfiguracionTiposTableSeeder"
     * @return void
     */
    public function run()
    {

        ImagenConfiguracionTipo::create([
            'nombre' => 'Logo Claro',
        ]);

        ImagenConfiguracionTipo::create([
            'nombre' => 'Logo Oscuro',
        ]);

        ImagenConfiguracionTipo::create([
            'nombre' => 'Fondo Login Claro',
        ]);

        ImagenConfiguracionTipo::create([
            'nombre' => 'Fondo Login Oscuro',
        ]);


    }
}
