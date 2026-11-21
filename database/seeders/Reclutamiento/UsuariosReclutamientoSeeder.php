<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Puesto;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosReclutamientoSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['gyreclutador1', 'Reclutador', 'Uno', 'Reclutador de Personal'],
            ['gyreclutador2', 'Reclutador', 'Dos', 'Reclutador de Personal'],
            ['gyreclutador3', 'Reclutador', 'Tres', 'Reclutador de Personal'],
            ['gyjeferrhh', 'Jefe', 'Recursos Humanos', 'Jefe de Recursos Humanos'],
            ['gysubjeferrhh', 'Subjefe', 'Recursos Humanos', 'Subjefe de Recursos Humanos'],
            ['gyanalistareclutamiento', 'Analista', 'Reclutamiento', 'Analista de Reclutamiento'],
        ];

        foreach ($usuarios as [$usuarioDominio, $nombre, $apellido, $puesto]) {
            User::updateOrCreate(
                ['usuario_dominio' => $usuarioDominio],
                [
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'password' => 'Gy202600',
                    'estado' => 'Activo',
                    'puesto_id' => Puesto::where('nombre', $puesto)->value('id'),
                    'ultimo_login_ip' => '',
                ]
            );
        }
    }
}
