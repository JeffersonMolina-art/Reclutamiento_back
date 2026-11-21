<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Puesto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VacantesSeeder extends Seeder
{
    /**
     * Requiere: UsuarisistemaSeeder, PuestosSeeder, VacantesEstadosSeeder y SedesSeeder.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Reclutamiento\VacantesSeeder"
     */
    public function run(): void
    {
        $userId = User::where('usuario_dominio', 'gyjefmolina')->value('id');
        $estadoPublicada = DB::table('reclutamiento.vacantes_estados')->where('codigo', 'publicada')->value('id');
        $estadoBorrador = DB::table('reclutamiento.vacantes_estados')->where('codigo', 'borrador')->value('id');
        $sedeMatriz = DB::table('reclutamiento.sede')->where('nombre', 'Casa Matriz')->value('id');
        $sedeZona10 = DB::table('reclutamiento.sede')->where('nombre', 'Agencia Zona 10')->value('id');

        $vacantes = [
            [
                'nombre' => 'Desarrollador Backend Laravel',
                'puesto' => 'Desarrollador Backend',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeMatriz,
                'descripcion' => 'Desarrollo y mantenimiento de APIs con Laravel y SQL Server.',
                'cantidad_plazas' => 2,
                'modalidad_trabajo' => 'hibrido',
                'salario_minimo' => 8000,
                'salario_maximo' => 12000,
                'requisitos' => [['Experiencia con Laravel', true], ['Conocimiento de SQL Server', true], ['Docker', false]],
                'caracteristicas' => [['Horario', 'L-V 8:00 a 17:00'], ['Prestaciones', 'De ley + bono']],
            ],
            [
                'nombre' => 'Desarrollador Frontend Vue',
                'puesto' => 'Desarrollador Frontend',
                'estado_id' => $estadoPublicada,
                'sede_id' => $sedeMatriz,
                'descripcion' => 'Desarrollo de interfaces web con Vue 3 y TypeScript.',
                'cantidad_plazas' => 1,
                'modalidad_trabajo' => 'remoto',
                'salario_minimo' => 7500,
                'salario_maximo' => 11000,
                'requisitos' => [['Experiencia con Vue 3', true], ['TypeScript', false]],
                'caracteristicas' => [['Horario', 'Flexible']],
            ],
            [
                'nombre' => 'Analista de Créditos',
                'puesto' => 'Analista de Créditos',
                'estado_id' => $estadoBorrador,
                'sede_id' => $sedeZona10,
                'descripcion' => 'Análisis y evaluación de solicitudes de crédito.',
                'cantidad_plazas' => 3,
                'modalidad_trabajo' => 'presencial',
                'salario_minimo' => 5000,
                'salario_maximo' => 7000,
                'requisitos' => [['Cierre de pensum en Banca o Finanzas', true]],
                'caracteristicas' => [['Horario', 'L-V 8:00 a 17:00']],
            ],
        ];

        foreach ($vacantes as $v) {
            if (DB::table('reclutamiento.vacantes')->where('nombre', $v['nombre'])->exists()) {
                continue;
            }

            $vacanteId = DB::table('reclutamiento.vacantes')->insertGetId([
                'estado_id' => $v['estado_id'],
                'puesto_id' => Puesto::where('nombre', $v['puesto'])->value('id'),
                'sede_id' => $v['sede_id'],
                'nombre' => $v['nombre'],
                'descripcion' => $v['descripcion'],
                'cantidad_plazas' => $v['cantidad_plazas'],
                'user_id' => $userId,
                'user_encargado' => $userId,
                'fecha_publicacion' => $v['estado_id'] === $estadoPublicada ? now()->toDateString() : null,
                'modalidad_trabajo' => $v['modalidad_trabajo'],
                'salario_minimo' => $v['salario_minimo'],
                'salario_maximo' => $v['salario_maximo'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($v['requisitos'] as [$nombre, $obligatorio]) {
                DB::table('reclutamiento.vacante_requisitos')->insert([
                    'vacante_id' => $vacanteId,
                    'nombre' => $nombre,
                    'es_obligatorio' => $obligatorio,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($v['caracteristicas'] as [$nombre, $valor]) {
                DB::table('reclutamiento.vacante_caracteristicas')->insert([
                    'vacante_id' => $vacanteId,
                    'nombre' => $nombre,
                    'valor' => $valor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
