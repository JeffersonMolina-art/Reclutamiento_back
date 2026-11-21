<?php

namespace Database\Seeders\Configuraciones;


use App\Models\SIGUA\User as UserSigua;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrarUsuariosSiguaTableSeeders extends Seeder
{

    /**
     * Auto generated seed file
     * php artisan db:seed --class="Database\Seeders\Configuraciones\MigrarUsuariosSiguaTableSeeders"
     * @return void
     */
    public function run(): void
    {

//        $sourceUsers = UserSigua::with('asociado:id')->get();
//
//        foreach ($sourceUsers as $user) {
//
//            if ($user->usuario_dominio == 'gysistemasigua' || $user->asociado == null) {
//                continue;
//            }
//
//            User::firstOrCreate(
//                ['usuario_dominio' => $user->usuario_dominio],
//                [
//                    'sigua_id' => $user->id,
//                    'asociado_id' => $user->asociado_id,
//                    'codigo_ejecutivo' => $user->codigo_ejecutivo,
//                    'nombre' => $user->nombre,
//                    'email' => $user->email,
//                    'apellido' => $user->apellido,
//                    'password' => entornoProduccion() ? $user->password : bcrypt('Gy202600'),
//                    'estado' => $user->estado,
//                    'puesto_id' => $user->puesto_id,
//                    'unidad_id' => $user->unidad_id,
//                    'region_id' => $user->region_id,
//                    'gerencia_id' => $user->gerencia_id,
//                    'password_changed_at' => $user->password_changed_at,
//                    'remember_token' => $user->remember_token,
//                    'ultimo_login_el' => $user->ultimo_login_el,
//                    'ultimo_login_ip' => $user->ultimo_login_ip,
//                    'deleted_at' => $user->deleted_at,
//                ]
//            );
//        }

    }
}
