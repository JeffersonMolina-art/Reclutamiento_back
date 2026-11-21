<?php

namespace Database\Seeders\Configuraciones;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsuarisistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['usuario_dominio' => 'gysistema'],
            [
                'nombre' => 'SISTEMA',
                'apellido' => 'RECLUTAMIENTO',
                'email' => 'servicioalcliente@micoopeguayacan.gt',
                'password' => 'admin',
                'estado' => 'Activo',
                'ultimo_login_ip' => '',
            ]
        );

        User::updateOrCreate(
            ['usuario_dominio' => 'gyjefmolina'],
            [
                'nombre' => 'Jefferson',
                'apellido' => 'Molina',
                'password' => 'Gy2026',
                'estado' => 'Activo',
                'ultimo_login_ip' => '',
            ]
        );
    }
}
