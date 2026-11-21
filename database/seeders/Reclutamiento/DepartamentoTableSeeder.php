<?php

    namespace Database\Seeders\Reclutamiento;

    use App\Models\Departamento;
    use Illuminate\Database\Seeder;

    class DepartamentoTableSeeder extends Seeder
    {
        public function run(): void
        {
            $departamentos = [
                'Alta Verapaz',
                'Baja Verapaz',
                'Chimaltenango',
                'Chiquimula',
                'El Progreso',
                'Escuintla',
                'Guatemala',
                'Huehuetenango',
                'Izabal',
                'Jalapa',
                'Jutiapa',
                'Petén',
                'Quetzaltenango',
                'Quiché',
                'Retalhuleu',
                'Sacatepéquez',
                'San Marcos',
                'Santa Rosa',
                'Sololá',
                'Suchitepéquez',
                'Totonicapán',
                'Zacapa',
            ];

            foreach ($departamentos as $nombre) {
                Departamento::updateOrCreate([
                    'nombre' => $nombre,
                    'estado' => 'Activo',
                ]);
            }
        }
    }
