<?php

    namespace Database\Seeders\Reclutamiento;

    use App\Models\Departamento;
    use App\Models\Municipio;
    use Illuminate\Database\Seeder;
    use RuntimeException;

    class MunicipioTableSeeder extends Seeder
    {
        public function run(): void
        {
            $municipiosPorDepartamento = [
                'Alta Verapaz' => ['Cobán', 'San Pedro Carchá'],
                'Baja Verapaz' => ['Salamá', 'Rabinal'],
                'Chimaltenango' => ['Chimaltenango', 'Tecpán Guatemala'],
                'Chiquimula' => ['Chiquimula', 'Esquipulas'],
                'El Progreso' => ['Guastatoya', 'Sanarate'],
                'Escuintla' => ['Escuintla', 'Santa Lucía Cotzumalguapa'],
                'Guatemala' => ['Guatemala', 'Mixco'],
                'Huehuetenango' => ['Huehuetenango', 'Chiantla'],
                'Izabal' => ['Puerto Barrios', 'Livingston'],
                'Jalapa' => ['Jalapa', 'San Pedro Pinula'],
                'Jutiapa' => ['Jutiapa', 'Asunción Mita'],
                'Petén' => ['Flores', 'San Benito'],
                'Quetzaltenango' => ['Quetzaltenango', 'Coatepeque'],
                'Quiché' => ['Santa Cruz del Quiché', 'Chichicastenango'],
                'Retalhuleu' => ['Retalhuleu', 'San Sebastián'],
                'Sacatepéquez' => ['Antigua Guatemala', 'Ciudad Vieja'],
                'San Marcos' => ['San Marcos', 'Malacatán'],
                'Santa Rosa' => ['Cuilapa', 'Barberena'],
                'Sololá' => ['Sololá', 'Panajachel'],
                'Suchitepéquez' => ['Mazatenango', 'Cuyotenango'],
                'Totonicapán' => ['Totonicapán', 'San Cristóbal Totonicapán'],
                'Zacapa' => ['Zacapa', 'Gualán'],
            ];

            $nombresDepartamentos = array_keys($municipiosPorDepartamento);

            $departamentos = Departamento::query()
                ->where('pais_id', 1)
                ->whereIn('nombre', $nombresDepartamentos)
                ->pluck('id', 'nombre')
                ->all();

            $faltantes = array_diff($nombresDepartamentos, array_keys($departamentos));

            if ($faltantes !== []) {
                throw new RuntimeException(
                    'Faltan departamentos para el país 1: ' . implode(', ', $faltantes)
                );
            }

            foreach ($municipiosPorDepartamento as $departamento => $municipios) {
                foreach ($municipios as $nombre) {
                    Municipio::updateOrCreate([
                        'departamento_id' => $departamentos[$departamento],
                        'nombre' => $nombre,
                    ]);
                }
            }
        }
    }
