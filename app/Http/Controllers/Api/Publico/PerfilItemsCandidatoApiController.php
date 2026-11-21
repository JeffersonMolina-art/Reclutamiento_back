<?php

namespace App\Http\Controllers\Api\Publico;

use App\Http\Controllers\AppBaseController;
use App\Models\Candidato;
use App\Models\CandidatoConocimiento;
use App\Models\CandidatoExperiencia;
use App\Models\CandidatoFormacion;
use App\Models\UserExterno;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PerfilItemsCandidatoApiController extends AppBaseController
{
    private const FECHAS = [
        'fecha_inicio' => 'required|date|after:1950-01-01|before_or_equal:today',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio|before_or_equal:today',
    ];

    public function index(Request $request, string $recurso): JsonResponse
    {
        $config = $this->config($recurso);

        return $this->executeOperation(function () use ($request, $config) {
            $items = $config['modelo']::query()
                ->where('candidato_id', $this->candidato($request)->id)
                ->orderBy($config['orden'], $config['direccion'])
                ->get(array_merge(['id'], array_keys($config['reglas'])));

            return response()->json(['data' => $items]);
        });
    }

    public function store(Request $request, string $recurso): JsonResponse
    {
        $config = $this->config($recurso);
        $datos = $request->validate($config['reglas']);

        return $this->executeTransaction(function () use ($request, $config, $datos) {
            $candidato = $this->candidato($request);

            if ($config['modelo']::where('candidato_id', $candidato->id)->count() >= $config['maximo']) {
                return response()->json(['message' => "Alcanzaste el máximo de {$config['maximo']} registros."], 422);
            }

            $item = $config['modelo']::create($datos + ['candidato_id' => $candidato->id]);

            return response()->json(['message' => 'Registro creado.', 'data' => Arr::only($item->toArray(), array_merge(['id'], array_keys($datos)))], 201);
        });
    }

    public function update(Request $request, string $recurso, int $id): JsonResponse
    {
        $config = $this->config($recurso);
        $datos = $request->validate($config['reglas']);

        return $this->executeTransaction(function () use ($request, $config, $datos, $id) {
            $item = $this->buscar($request, $config['modelo'], $id);

            if (! $item) {
                return response()->json(['message' => 'Registro no encontrado.'], 404);
            }

            $item->fill($datos)->save();

            return response()->json(['message' => 'Registro actualizado.', 'data' => Arr::only($item->toArray(), array_merge(['id'], array_keys($datos)))]);
        });
    }

    public function destroy(Request $request, string $recurso, int $id): JsonResponse
    {
        $config = $this->config($recurso);

        return $this->executeTransaction(function () use ($request, $config, $id) {
            $item = $this->buscar($request, $config['modelo'], $id);

            if (! $item) {
                return response()->json(['message' => 'Registro no encontrado.'], 404);
            }

            $item->delete();

            return response()->json(['message' => 'Registro eliminado.']);
        });
    }

    private function buscar(Request $request, string $modelo, int $id): ?Model
    {
        return $modelo::query()
            ->where('id', $id)
            ->where('candidato_id', $this->candidato($request)->id)
            ->first();
    }

    private function config(string $recurso): array
    {
        return match ($recurso) {
            'conocimientos' => [
                'modelo' => CandidatoConocimiento::class,
                'orden' => 'id', 'direccion' => 'asc', 'maximo' => 50,
                'reglas' => ['descripcion' => 'required|string|max:255', 'nivel' => 'nullable|string|max:45'],
            ],
            'experiencias' => [
                'modelo' => CandidatoExperiencia::class,
                'orden' => 'fecha_inicio', 'direccion' => 'desc', 'maximo' => 30,
                'reglas' => ['empresa' => 'required|string|max:255', 'puesto' => 'nullable|string|max:45'] + self::FECHAS,
            ],
            'formaciones' => [
                'modelo' => CandidatoFormacion::class,
                'orden' => 'fecha_inicio', 'direccion' => 'desc', 'maximo' => 30,
                'reglas' => ['nombre' => 'required|string|max:255', 'titulo_carrera' => 'nullable|string|max:255'] + self::FECHAS,
            ],
            default => abort(404),
        };
    }

    private function candidato(Request $request): Candidato
    {
        /** @var UserExterno $usuario */
        $usuario = $request->user();

        return $usuario->candidato()->firstOrFail();
    }
}
