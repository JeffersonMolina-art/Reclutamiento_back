<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Puesto;
use App\Models\Sede;
use App\Models\Unidad;
use App\Models\User;
use App\Models\Vacante;
use App\Models\VacanteEstado;
use Illuminate\Database\Seeder;

class VacantesSeeder extends Seeder
{
    /**
     * Requiere: UsuarisistemaSeeder, PuestoTableSeeder, VacantesEstadosSeeder y SedesSeeder.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\VacantesSeeder"
     */
    public function run(): void
    {
        $userId = User::where('usuario_dominio', 'gyreclutador1')->value('id');
        $estadoPublicada = VacanteEstado::where('codigo', 'publicada')->value('id');
        $sedeJalapa = Sede::where('nombre', 'Agencia Jalapa')->value('id');
        $sedeGuastatoya = Sede::where('nombre', 'Agencia Guastatoya')->value('id');
        $unidadReclutamiento = Unidad::where('codigo', 'RCL')->value('id');
        $unidadDesarrollo = Unidad::where('codigo', 'DES')->value('id');
        $unidadInfraestructura = Unidad::where('codigo', 'INF')->value('id');
        $unidadContabilidad = Unidad::where('codigo', 'CON')->value('id');
        $unidadCreditos = Unidad::where('codigo', 'CRE')->value('id');

        $vacantes = [
            [
                'nombre' => 'Desarrollador de Software',
                'puesto' => 'Desarrollador de Software',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeJalapa,
                'unidad_id' => $unidadDesarrollo,
                'descripcion' => 'Desarrollo y mantenimiento de aplicaciones internas.',
                'cantidad_plazas' => 2,
                'modalidad_trabajo' => 'hibrido',
                'salario_minimo' => 9000,
                'salario_maximo' => 14000,
                'requisitos' => [['Experiencia en PHP y Laravel', true], ['Conocimiento de SQL Server', true], ['Manejo de Vue.js', false]],
                'caracteristicas' => [['Horario', 'L-V 8:00 a 17:00'], ['Prestaciones', 'De ley + bono']],
            ],
            [
                'nombre' => 'Técnico de Soporte',
                'puesto' => 'Técnico de Soporte',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeGuastatoya,
                'unidad_id' => $unidadInfraestructura,
                'descripcion' => 'Soporte técnico a usuarios y mantenimiento de equipos y redes.',
                'cantidad_plazas' => 2,
                'modalidad_trabajo' => 'presencial',
                'salario_minimo' => 5500,
                'salario_maximo' => 8000,
                'requisitos' => [['Conocimientos de redes y hardware', true], ['Experiencia en soporte técnico', false]],
                'caracteristicas' => [['Horario', 'L-V 8:00 a 17:00']],
            ],
            [
                'nombre' => 'Contador General',
                'puesto' => 'Contador General',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeJalapa,
                'unidad_id' => $unidadContabilidad,
                'descripcion' => 'Registro contable, conciliaciones y cierres mensuales.',
                'cantidad_plazas' => 1,
                'modalidad_trabajo' => 'presencial',
                'salario_minimo' => 8000,
                'salario_maximo' => 12000,
                'requisitos' => [['Contador Público y Auditor colegiado activo', true], ['Experiencia en cierres contables', true]],
                'caracteristicas' => [['Horario', 'L-V 8:00 a 17:00']],
            ],
            [
                'nombre' => 'Analista de Créditos',
                'puesto' => 'Analista de Créditos',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeGuastatoya,
                'unidad_id' => $unidadCreditos,
                'descripcion' => 'Evaluación y seguimiento de solicitudes de crédito.',
                'cantidad_plazas' => 3,
                'modalidad_trabajo' => 'presencial',
                'salario_minimo' => 6000,
                'salario_maximo' => 9000,
                'requisitos' => [['Experiencia en análisis crediticio', true], ['Cierre de pensum en Banca o Administración', true]],
                'caracteristicas' => [['Horario', 'L-V 8:00 a 17:00'], ['Prestaciones', 'De ley + comisión']],
            ],
            [
                'nombre' => 'Analista de Reclutamiento',
                'puesto' => 'Analista de Reclutamiento',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeJalapa,
                'unidad_id' => $unidadReclutamiento,
                'descripcion' => 'Análisis de perfiles y gestión de procesos de reclutamiento.',
                'cantidad_plazas' => 3,
                'modalidad_trabajo' => 'hibrido',
                'salario_minimo' => 8000,
                'salario_maximo' => 12000,
                'requisitos' => [['Experiencia en reclutamiento y selección', true], ['Conocimiento de entrevistas por competencias', true], ['Manejo de Excel', false]],
                'caracteristicas' => [['Horario', 'L-V 8:00 a 17:00'], ['Prestaciones', 'De ley + bono']],
            ],
            [
                'nombre' => 'Reclutador de Personal',
                'puesto' => 'Reclutador de Personal',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeJalapa,
                'unidad_id' => $unidadReclutamiento,
                'descripcion' => 'Captación de candidatos y coordinación de entrevistas.',
                'cantidad_plazas' => 1,
                'modalidad_trabajo' => 'remoto',
                'salario_minimo' => 7500,
                'salario_maximo' => 11000,
                'requisitos' => [['Experiencia en captación de personal', true], ['Manejo de redes profesionales', false]],
                'caracteristicas' => [['Horario', 'Flexible']],
            ],
            [
                'nombre' => 'Asistente de Recursos Humanos',
                'puesto' => 'Asistente de Recursos Humanos',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeGuastatoya,
                'unidad_id' => $unidadReclutamiento,
                'descripcion' => 'Apoyo administrativo en procesos de Recursos Humanos.',
                'cantidad_plazas' => 3,
                'modalidad_trabajo' => 'presencial',
                'salario_minimo' => 5000,
                'salario_maximo' => 7000,
                'requisitos' => [['Cierre de pensum en Administración o Psicología', true]],
                'caracteristicas' => [['Horario', 'L-V 8:00 a 17:00']],
            ],
        ];

        foreach ($vacantes as $v) {
            if (Vacante::where('nombre', $v['nombre'])->exists()) {
                continue;
            }

            $vacante = Vacante::create([
                'estado_id' => $v['estado_id'],
                'puesto_id' => Puesto::where('nombre', $v['puesto'])->value('id'),
                'sede_id' => $v['sede_id'],
                'unidad_id' => $v['unidad_id'],
                'nombre' => $v['nombre'],
                'descripcion' => $v['descripcion'],
                'cantidad_plazas' => $v['cantidad_plazas'],
                'user_id' => $userId,
                'user_encargado' => $userId,
                'fecha_publicacion' => $v['estado_id'] === $estadoPublicada ? now()->toDateString() : null,
                'modalidad_trabajo' => $v['modalidad_trabajo'],
                'salario_minimo' => $v['salario_minimo'],
                'salario_maximo' => $v['salario_maximo'],
            ]);

            foreach ($v['requisitos'] as [$nombre, $obligatorio]) {
                $vacante->requisitos()->create([
                    'nombre' => $nombre,
                    'es_obligatorio' => $obligatorio,
                ]);
            }

            foreach ($v['caracteristicas'] as [$nombre, $valor]) {
                $vacante->caracteristicas()->create([
                    'nombre' => $nombre,
                    'valor' => $valor,
                ]);
            }
        }
    }
}
