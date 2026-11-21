<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Vacante;
use App\Models\VacanteEstado;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class VacanteApiController
 */
class VacanteApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver vacantes', only: ['index', 'show']),
            new Middleware('abilities:crear vacantes', only: ['store']),
            new Middleware('abilities:editar vacantes', only: ['update']),
            new Middleware('abilities:eliminar vacantes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Vacantes.
     * GET|HEAD /vacantes
     */
    public function index(): JsonResponse
    {
        return $this->executeOperation(function () {
            $vacantes = QueryBuilder::for(Vacante::class)
                ->allowedFilters(
                    AllowedFilter::exact('id'),
                    AllowedFilter::exact('estado_id'),
                    AllowedFilter::exact('puesto_id'),
                    AllowedFilter::exact('sede_id'),
                    AllowedFilter::exact('modalidad_trabajo'),
                    'nombre'
                )
                ->allowedSorts(
                    'id',
                    'nombre',
                    'estado_id',
                    'cantidad_plazas',
                    'fecha_publicacion',
                    'created_at'
                )
                ->allowedIncludes('estado', 'puesto', 'sede', 'requisitos', 'caracteristicas')
                ->defaultSort('-id')
                ->jsonPaginate(request('page.size') ?? 10);

            return $this->sendResponse($vacantes->toArray(), 'vacantes recuperadas con éxito.');
        });
    }

    public function store(Request $request): JsonResponse
    {
        $validados = $request->validate([
            'puesto_id' => ['required', 'integer'],
            'sede_id' => ['required', 'integer'],
            'user_encargado' => ['nullable', 'integer'],
            'nombre' => ['required', 'string', 'max:45'],
            'descripcion' => ['required', 'string', 'max:255'],
            'cantidad_plazas' => ['required', 'integer', 'min:1'],
            'modalidad_trabajo' => ['required', Rule::in(['presencial', 'remoto', 'hibrido'])],
            'salario_minimo' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'salario_maximo' => ['required', 'numeric', 'gte:salario_minimo', 'decimal:0,2'],
            'requisitos' => ['sometimes', 'array'],
            'requisitos.*' => ['array:nombre,es_obligatorio'],
            'requisitos.*.nombre' => ['required', 'string', 'max:255'],
            'requisitos.*.es_obligatorio' => ['required', 'boolean'],
            'caracteristicas' => ['sometimes', 'array'],
            'caracteristicas.*' => ['array:nombre,valor'],
            'caracteristicas.*.nombre' => ['required', 'string', 'max:255'],
            'caracteristicas.*.valor' => ['required', 'string', 'max:45'],
        ]);

        return $this->executeTransaction(function () use ($request, $validados) {
            $validados['user_id'] = $request->user()->id;
            $validados['estado_id'] = VacanteEstado::BORRADOR;

            $vacante = Vacante::create($validados);

            $this->sincronizarHijos($vacante, $validados);

            $comentario = implode("\n", [
                "Se creó la vacante «{$vacante->nombre}».",
                "Puesto: {$vacante->puesto?->nombre} · Sede: {$vacante->sede?->nombre}",
                "Modalidad: {$vacante->modalidad_trabajo} · Plazas: {$vacante->cantidad_plazas}",
                'Estado inicial: ' . ($vacante->estado?->nombre ?? 'Sin definir'),
                'Encargado: ' . ($vacante->encargado?->nombre_completo ?? 'Sin asignar'),
                'Rango salarial: ' . ($vacante->rango_salarial ?? 'Sin definir'),
            ]);

            $vacante->addBitacora(
                'Vacante',
                'Vacante creada',
                $comentario,
                'tabler-file-plus',
                'success'
            );

            return $this->sendResponse(
                $vacante->load('estado', 'puesto', 'sede', 'requisitos', 'caracteristicas')->toArray(),
                'Vacante creada con éxito.'
            );
        });
    }

    /**
     * Display the specified Vacante.
     * GET|HEAD /vacantes/{id}
     */
    public function show(Vacante $vacante): JsonResponse
    {
        return $this->executeOperation(function () use ($vacante) {
            $vacante->load('estado', 'puesto', 'sede', 'requisitos', 'caracteristicas', 'bitacoras');

            return $this->sendResponse($vacante->toArray(), 'Vacante recuperada con éxito.');
        });
    }

    /**
     * Update the specified Vacante in storage.
     * PUT/PATCH /vacantes/{id}
     */
    public function update(Request $request, Vacante $vacante): JsonResponse
    {
        $validados = $request->validate([
            'estado_id' => ['required', 'integer'],
            'puesto_id' => ['required', 'integer'],
            'sede_id' => ['required', 'integer'],
            'user_encargado' => ['nullable', 'integer'],
            'nombre' => ['required', 'string', 'max:45'],
            'descripcion' => ['required', 'string', 'max:255'],
            'cantidad_plazas' => ['required', 'integer', 'min:1'],
            'fecha_publicacion' => ['nullable', 'date'],
            'modalidad_trabajo' => ['required', Rule::in(['presencial', 'remoto', 'hibrido'])],
            'salario_minimo' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'salario_maximo' => ['required', 'numeric', 'gte:salario_minimo', 'decimal:0,2'],
            'requisitos' => ['sometimes', 'array'],
            'requisitos.*' => ['array'],
            'requisitos.*.id' => ['nullable', 'integer', 'min:1', 'distinct'],
            'requisitos.*.nombre' => ['required', 'string', 'max:255'],
            'requisitos.*.es_obligatorio' => ['required', 'boolean'],
            'caracteristicas' => ['sometimes', 'array'],
            'caracteristicas.*' => ['array'],
            'caracteristicas.*.id' => ['nullable', 'integer', 'min:1', 'distinct'],
            'caracteristicas.*.nombre' => ['required', 'string', 'max:255'],
            'caracteristicas.*.valor' => ['required', 'string', 'max:45'],
        ]);

        if (array_key_exists('requisitos', $validados)) {
            $validados['requisitos'] = array_map(
                fn (array $fila) => [
                    'id' => $fila['id'] ?? null,
                    'nombre' => $fila['nombre'],
                    'es_obligatorio' => $fila['es_obligatorio'],
                ],
                $validados['requisitos']
            );
        }

        if (array_key_exists('caracteristicas', $validados)) {
            $validados['caracteristicas'] = array_map(
                fn (array $fila) => [
                    'id' => $fila['id'] ?? null,
                    'nombre' => $fila['nombre'],
                    'valor' => $fila['valor'],
                ],
                $validados['caracteristicas']
            );
        }

        return $this->executeTransaction(function () use ($vacante, $validados) {
            $validados = $this->completarFechaPublicacion($validados, $vacante);

            $vacante->fill(array_intersect_key(
                $validados,
                array_flip($vacante->getFillable())
            ));

            $comentario = $vacante->generarComentarioActualizacion();
            $vacante->estado_id = VacanteEstado::PUBLICADA;
            $vacante->save();

            $this->sincronizarHijos($vacante, $validados);

            if (array_key_exists('requisitos', $validados)) {
                $comentario .= ' | Se actualizaron los requisitos';
            }

            if (array_key_exists('caracteristicas', $validados)) {
                $comentario .= ' | Se actualizaron las características';
            }

            $vacante->addBitacora(
                'Vacante',
                'Vacante actualizada',
                $comentario,
                'tabler-edit',
                'primary'
            );

            return $this->sendResponse(
                $vacante->load('estado', 'puesto', 'sede', 'requisitos', 'caracteristicas')->toArray(),
                'Vacante actualizada con éxito.'
            );
        });
    }

    /**
     * Remove the specified Vacante from storage.
     * DELETE /vacantes/{id}
     */
    public function destroy(Vacante $vacante): JsonResponse
    {
        return $this->executeTransaction(function () use ($vacante) {
            if ($vacante->postulaciones()->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar la vacante porque tiene postulaciones registradas.',
                ], 409);
            }
            $vacante->addBitacora(
                'Vacante',
                'Vacante eliminada',
                $vacante->generarComentarioEliminacion(),
                'tabler-trash',
                'error'
            );
            $vacante->delete();

            return $this->sendResponse(null, 'Vacante eliminada con éxito.');
        });
    }

    private function sincronizarHijos(Vacante $vacante, array $validados): void
    {
        if (array_key_exists('requisitos', $validados)) {
            $this->sincronizarFilas(
                $vacante->requisitos(),
                $validados['requisitos'],
                'requisitos'
            );
        }

        if (array_key_exists('caracteristicas', $validados)) {
            $this->sincronizarFilas(
                $vacante->caracteristicas(),
                $validados['caracteristicas'],
                'caracteristicas'
            );
        }
    }

    private function sincronizarFilas(HasMany $relacion, array $filas, string $campo): void
    {
        $existentes = $relacion->get()->keyBy('id');
        $idsConservados = [];

        foreach ($filas as $indice => $fila) {
            $id = $fila['id'] ?? null;
            unset($fila['id']);

            if ($id === null) {
                $relacion->create($fila);
                continue;
            }

            $registro = $existentes->get($id);

            if ($registro === null) {
                throw ValidationException::withMessages(["{$campo}.{$indice}.id" => 'Este registro no pertenece a la vacante.',]);
            }

            $registro->update($fila);
            $idsConservados[] = $registro->getKey();
        }

        foreach ($existentes->except($idsConservados) as $registro) {
            $registro->delete();
        }
    }

    private function completarFechaPublicacion(array $datos, ?Vacante $vacante = null): array
    {
        $estadoId = $datos['estado_id'] ?? $vacante?->estado_id;
        $codigo = VacanteEstado::whereKey($estadoId)->value('codigo');
        $fechaActual = $datos['fecha_publicacion'] ?? $vacante?->fecha_publicacion;

        if ($codigo === Vacante::ESTADO_PUBLICADA && empty($fechaActual)) {
            $datos['fecha_publicacion'] = now()->toDateString();
        }

        return $datos;
    }
}
