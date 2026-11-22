<?php

namespace Database\Seeders\Reclutamiento;

use App\Models\Candidato;
use App\Models\UserExterno;
use App\Models\UserExternoEstado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserExternoSeeder extends Seeder
{
    /**
     * Usuario externo de prueba con su candidato. El CV se sube manualmente.
     * Requiere UserExternoEstadoTableSeeder.
     */
    public function run(): void
    {
        $usuario = UserExterno::updateOrCreate(
            ['email' => 'exampler@gmail.com'],
            [
                'telefono' => '55550000',
                'password' => Hash::make('Gy202600'),
                'estado_id' => UserExternoEstado::where('codigo', 'ACT')->value('id') ?? UserExterno::ESTADO_ACTIVO,
                'email_verified_at' => now(),
                'telefono_verified_at' => now(),
            ]
        );

        $candidato = Candidato::updateOrCreate(
            ['user_id' => $usuario->id],
            [
                'primer_nombre' => 'Juan',
                'segundo_nombre' => 'Carlos',
                'primer_apellido' => 'Pérez',
                'segundo_apellido' => 'López',
                'dpi' => '2500000000101',
                'correo_contacto' => 'exampler@gmail.com',
                'telefono_contacto' => '55550000',
                'fecha_nacimiento' => '1998-05-15',
                'expectativa_salarial' => 6000,
            ]
        );

        $candidato->conocimientos()->delete();
        $candidato->conocimientos()->createMany([
            ['descripcion' => 'Microsoft Excel', 'nivel' => 'Avanzado'],
            ['descripcion' => 'Atención al cliente', 'nivel' => 'Intermedio'],
            ['descripcion' => 'Contabilidad básica', 'nivel' => 'Intermedio'],
        ]);

        $candidato->experiencias()->delete();
        $candidato->experiencias()->createMany([
            ['empresa' => 'Comercial El Éxito', 'puesto' => 'Auxiliar administrativo', 'fecha_inicio' => '2019-02-01', 'fecha_fin' => '2021-06-30'],
            ['empresa' => 'Servicios Financieros S.A.', 'puesto' => 'Analista de créditos', 'fecha_inicio' => '2021-07-01', 'fecha_fin' => null],
        ]);

        $candidato->formaciones()->delete();
        $candidato->formaciones()->createMany([
            ['nombre' => 'Colegio Nacional', 'titulo_carrera' => 'Perito Contador', 'fecha_inicio' => '2013-01-01', 'fecha_fin' => '2015-11-30'],
            ['nombre' => 'Universidad de San Carlos de Guatemala', 'titulo_carrera' => 'Licenciatura en Administración de Empresas', 'fecha_inicio' => '2016-01-01', 'fecha_fin' => '2021-12-15'],
        ]);
    }
}
