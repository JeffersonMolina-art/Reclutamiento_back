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
            ['gymadruano', 'Madelyn Jazmine', 'Ruano Hernandez', 'Reclutador de Personal'],
            ['gyfermoran', 'Fernando Jose', 'Moran Morales', 'Reclutador de Personal'],
            ['gyemidardon', 'Emily Jasmine', 'Dardon Cabrera', 'Reclutador de Personal'],
            ['gyvivjacinto', 'Vivian Maribel', 'Jacinto Calderon', 'Jefe de Recursos Humanos'],
            ['gycrigarcia', 'Cristian Jose', 'Garcia Rosales', 'Subjefe de Recursos Humanos'],
            ['gysaumonterroso', 'Saul Rodrigo', 'Monterroso Belteton', 'Analista de Reclutamiento'],
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
