<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Gerencia;
use App\Models\Unidad;
use Illuminate\Database\Seeder;

class UnidadesSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\UnidadesSeeder"
     */
    public function run(): void
    {
        $unidades = [
            ['codigo' => 'DES', 'nombre' => 'Desarrollo de Software', 'gerencia' => 'GTI'],
            ['codigo' => 'INF', 'nombre' => 'Infraestructura y Soporte', 'gerencia' => 'GTI'],
            ['codigo' => 'RCL', 'nombre' => 'Reclutamiento y Selección', 'gerencia' => 'GRH'],
            ['codigo' => 'CON', 'nombre' => 'Contabilidad', 'gerencia' => 'GFI'],
            ['codigo' => 'CRE', 'nombre' => 'Créditos', 'gerencia' => 'GNE'],
        ];

        foreach ($unidades as $unidad) {
            $gerenciaId = Gerencia::where('codigo', $unidad['gerencia'])->value('id');

            Unidad::updateOrCreate(
                ['codigo' => $unidad['codigo']],
                ['nombre' => $unidad['nombre'], 'gerencia_id' => $gerenciaId, 'estado' => 'Activo']
            );
        }
    }
}
