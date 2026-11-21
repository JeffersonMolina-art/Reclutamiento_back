<?php

    namespace Database\Seeders\Reclutamiento;

    use App\Models\Puesto;
    use Illuminate\Database\Seeder;

    class PuestoTableSeeder extends Seeder
    {
        public function run(): void
        {
            $puestos = [
                'Analista de Reclutamiento',
                'Secretario de Recursos Humanos',
                'Jefe de Recursos Humanos',
                'Reclutador de Personal',
                'Coordinador de Reclutamiento y Selección',
                'Asistente de Recursos Humanos',
                'Analista de Nómina',
                'Analista de Compensación y Beneficios',
                'Coordinador de Capacitación',
                'Analista de Desarrollo Organizacional',
            ];

            foreach ($puestos as $nombre) {
                Puesto::updateOrCreate(['nombre' => $nombre]);
            }
        }
    }
