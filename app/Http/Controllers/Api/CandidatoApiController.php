<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Candidato;
use App\Models\Postulacion;
use App\Models\UserExterno;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
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
            new Middleware('abilities:ver candidatos', only: ['index']),
            new Middleware('abilities:historial candidatos', only: ['historial']),
            new Middleware('abilities:crear candidatos', only: ['store']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $consulta = Candidato::query()
                ->withCount('postulaciones')
                ->with(['postulaciones' => fn ($q) => $q->latest('fecha_postulacion')->latest('id')
                    ->with(['vacante:id,nombre', 'estado:id,nombre,codigo'])]);

            $candidatos = QueryBuilder::for($consulta, $request)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    AllowedFilter::callback('buscar', fn ($q, $valor) => $q->where(
                        fn ($w) => $w->where('dpi', 'like', "%{$valor}%")
                            ->orWhere('correo_contacto', 'like', "%{$valor}%")
                            ->orWhere('telefono_contacto', 'like', "%{$valor}%")
                            ->orWhere('primer_nombre', 'like', "%{$valor}%")
                            ->orWhere('segundo_nombre', 'like', "%{$valor}%")
                            ->orWhere('primer_apellido', 'like', "%{$valor}%")
                            ->orWhere('segundo_apellido', 'like', "%{$valor}%")
                    )),
                    AllowedFilter::callback('vacante_id', fn ($q, $valor) => $q->whereHas(
                        'postulaciones', fn ($p) => $p->where('vacante_id', $valor)
                    )),
                )
                ->allowedSorts('id', 'primer_nombre', 'primer_apellido', 'created_at')
                ->defaultSort('-id')
                ->jsonPaginate($request->input('page.size') ?? 10)
                ->through(fn (Candidato $c) => [
                    'id' => $c->id,
                    'nombre_completo' => $c->nombre_completo,
                    'dpi' => $c->dpi,
                    'correo' => $c->correo_contacto,
                    'telefono' => $c->telefono_contacto,
                    'expectativa_salarial' => $c->expectativa_salarial,
                    'total_postulaciones' => $c->postulaciones_count,
                    'postulaciones' => $c->postulaciones->map(fn ($p) => [
                        'id' => $p->id,
                        'vacante_id' => $p->vacante_id,
                        'vacante' => $p->vacante?->nombre,
                        'estado' => $p->estado?->nombre,
                        'estado_codigo' => $p->estado?->codigo,
                        'fecha_postulacion' => $p->fecha_postulacion?->toDateString(),
                    ])->values(),
                ]);

            return $this->sendResponse($candidatos->toArray(), 'Candidatos recuperados con éxito.');
        });
    }
    public function historial(Candidato $candidato): JsonResponse
    {
        return $this->executeOperation(function () use ($candidato) {
            $postulaciones = $candidato->postulaciones()
                ->with([
                    'vacante:id,nombre,puesto_id,sede_id',
                    'vacante.puesto:id,nombre',
                    'vacante.sede:id,nombre',
                    'estado:id,nombre,codigo',
                    'seguimientos' => fn ($q) => $q->orderBy('fecha_cambio')->orderBy('id'),
                    'seguimientos.estadoActual:id,nombre,codigo',
                    'seguimientos.user',
                ])
                ->latest('fecha_postulacion')
                ->latest('id')
                ->get()
                ->map(fn (Postulacion $p) => [
                    'id' => $p->id,
                    'vacante' => $p->vacante?->nombre,
                    'puesto' => $p->vacante?->puesto?->nombre,
                    'sede' => $p->vacante?->sede?->nombre,
                    'estado' => $p->estado?->nombre,
                    'estado_codigo' => $p->estado?->codigo,
                    'fecha_postulacion' => $p->fecha_postulacion?->toDateString(),
                    'porcentaje' => $p->seguimientos->last()?->porcentaje ?? 0,
                    'seguimiento' => $p->seguimientos->map(fn ($s) => [
                        'estado' => $s->estadoActual?->nombre,
                        'estado_codigo' => $s->estadoActual?->codigo,
                        'porcentaje' => $s->porcentaje,
                        'comentario' => $s->comentario,
                        'usuario' => $s->user?->nombre_completo,
                        'fecha' => $s->fecha_cambio?->toIso8601String(),
                    ])->values(),
                ])
                ->values();

            return $this->sendResponse([
                'candidato' => [
                    'id' => $candidato->id,
                    'nombre_completo' => $candidato->nombre_completo,
                    'dpi' => $candidato->dpi,
                    'correo' => $candidato->correo_contacto,
                    'telefono' => $candidato->telefono_contacto,
                ],
                'postulaciones' => $postulaciones,
            ], 'Historial recuperado con éxito.');
        });
    }
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
