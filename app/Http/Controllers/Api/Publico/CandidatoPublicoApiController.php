<?php

namespace App\Http\Controllers\Api\Publico;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Publico\RegistroCandidatoRequest;
use App\Models\Candidato;
use App\Models\UserExterno;
use App\Models\UsuarioExternoEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class CandidatoPublicoApiController extends AppBaseController
{
    /**
     * POST /api/publico/candidatos/registro
     */
    public function registro(RegistroCandidatoRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $existeCorreo = UserExterno::query()
            ->where('email', $datos['email'])
            ->exists();

        if ($existeCorreo || Candidato::query()->where('dpi', $datos['dpi'])->exists()) {
            return response()->json([
                'message' => 'Ya existe otro usuario registrado con esa información.',
            ], 422);
        }

        return $this->executeTransaction(function () use ($datos) {
            $usuario = UserExterno::create([
                'email' => $datos['email'],
                'telefono' => $datos['telefono'],
                'password' => Hash::make($datos['password']),
                'estado_id' => UserExterno::ESTADO_ACTIVO,
            ]);

            Candidato::create([
                'user_id' => $usuario->id,
                'dpi' => $datos['dpi'],
                'primer_nombre' => $datos['primer_nombre'],
                'segundo_nombre' => $datos['segundo_nombre'] ?? null,
                'tercer_nombre' => $datos['tercer_nombre'] ?? null,
                'primer_apellido' => $datos['primer_apellido'],
                'segundo_apellido' => $datos['segundo_apellido'] ?? null,
                'correo_contacto' => $datos['email'],
                'telefono_contacto' => $datos['telefono'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'expectativa_salarial' => 0,
            ]);

            return response()->json(['message' => 'Registro exitoso.'], 201);
        });
    }
}
