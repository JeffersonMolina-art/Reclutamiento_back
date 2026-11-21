<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Gerencia;
use Illuminate\Database\Seeder;

class GerenciasSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\GerenciasSeeder"
     */
    public function run(): void
    {
        $gerencias = [
            ['codigo' => 'GTI', 'nombre' => 'Gerencia de Tecnología'],
            ['codigo' => 'GRH', 'nombre' => 'Gerencia de Recursos Humanos'],
            ['codigo' => 'GFI', 'nombre' => 'Gerencia Financiera'],
            ['codigo' => 'GNE', 'nombre' => 'Gerencia de Negocios'],
        ];

        foreach ($gerencias as $gerencia) {
            Gerencia::updateOrCreate(
                ['codigo' => $gerencia['codigo']],
                ['nombre' => $gerencia['nombre'], 'estado' => 'Activo']
            );
        }
    }
}
