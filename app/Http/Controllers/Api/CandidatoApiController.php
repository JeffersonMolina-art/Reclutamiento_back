<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Candidato;
use App\Models\UserExterno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CandidatoApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:crear postulaciones', only: ['store']),
        ];
    }

    /**
     * Crea un candidato junto con su usuario del portal.
     * POST /reclutamiento/candidatos
     */
    public function store(Request $request): JsonResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
            'dpi' => preg_replace('/\s+/', '', (string) $request->input('dpi')),
            'telefono' => preg_replace('/[\s-]+/', '', (string) $request->input('telefono')),
        ]);

        $datos = $request->validate([
            'primer_nombre' => ['required', 'string', 'min:2', 'max:45'],
            'segundo_nombre' => ['nullable', 'string', 'max:45'],
            'tercer_nombre' => ['nullable', 'string', 'max:45'],
            'primer_apellido' => ['required', 'string', 'min:2', 'max:45'],
            'segundo_apellido' => ['nullable', 'string', 'max:45'],
            'dpi' => ['required', 'digits:13'],
            'email' => ['required', 'string', 'email:rfc', 'max:100'],
            'telefono' => ['required', 'regex:/^\+?[0-9]{8,11}$/'],
            'fecha_nacimiento' => ['required', 'date_format:Y-m-d', 'after:1900-01-01', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'expectativa_salarial' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
        ], [
            'dpi.digits' => 'El DPI debe tener 13 dígitos.',
            'telefono.regex' => 'El teléfono debe tener entre 8 y 11 dígitos.',
            'fecha_nacimiento.before_or_equal' => 'El candidato debe ser mayor de 18 años.',
        ]);

        return $this->executeTransaction(function () use ($datos) {
            if (UserExterno::where('email', $datos['email'])->exists()) {
                return response()->json(['message' => 'Ya existe una cuenta con ese correo.', 'errors' => ['email' => ['Ya existe una cuenta con ese correo.']]], 422);
            }

            if (Candidato::where('dpi', $datos['dpi'])->exists()) {
                return response()->json(['message' => 'Ya existe un candidato con ese DPI.', 'errors' => ['dpi' => ['Ya existe un candidato con ese DPI.']]], 422);
            }

            $passwordTemporal = 'Gy202600';

            $usuario = UserExterno::create([
                'email' => $datos['email'],
                'telefono' => $datos['telefono'],
                'password' => Hash::make($passwordTemporal),
                'estado_id' => UserExterno::ESTADO_ACTIVO,
            ]);

            $candidato = Candidato::create([
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
                'expectativa_salarial' => $datos['expectativa_salarial'] ?? 0,
            ]);

            return $this->sendResponse([
                ...$candidato->toArray(),
                'password_temporal' => $passwordTemporal,
            ], 'Candidato creado con éxito.');
        });
    }
}
