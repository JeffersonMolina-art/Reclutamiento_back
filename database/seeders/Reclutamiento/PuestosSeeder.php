<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Gerencia;
use App\Models\Puesto;
use Illuminate\Database\Seeder;

class PuestosSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\PuestosSeeder"
     */
    public function run(): void
    {
        $puestos = [
            ['nombre' => 'Desarrollador Backend', 'gerencia' => 'GTI'],
            ['nombre' => 'Desarrollador Frontend', 'gerencia' => 'GTI'],
            ['nombre' => 'Analista de Soporte Técnico', 'gerencia' => 'GTI'],
            ['nombre' => 'Analista de Reclutamiento', 'gerencia' => 'GRH'],
            ['nombre' => 'Contador General', 'gerencia' => 'GFI'],
            ['nombre' => 'Analista de Créditos', 'gerencia' => 'GNE'],
        ];

        foreach ($puestos as $puesto) {
            Puesto::updateOrCreate(
                ['nombre' => $puesto['nombre']],
                [
                    'gerencia_id' => Gerencia::where('codigo', $puesto['gerencia'])->value('id'),
                    'estado' => 'Activo',
                ]
            );
        }
    }
}
