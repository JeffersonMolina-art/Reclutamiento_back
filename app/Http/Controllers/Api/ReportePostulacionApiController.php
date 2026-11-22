<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Postulacion;
use App\Models\PostulacionEstado;
use App\Models\Vacante;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportePostulacionApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:reportes postulaciones'),
        ];
    }

    public function postulacionesPorEstado(Request $request)
    {
        $filtros = $this->validarFiltros($request);
        $estados = $this->obtenerEstados($filtros['estado_ids']);
        $postulaciones = $this->obtenerPostulaciones($filtros);

        $pdf = Pdf::loadView('reportes.postulaciones_por_estado', [
            'vacantes' => $this->agruparPorVacante($postulaciones, $estados),
            'estados' => $estados,
            'total' => $postulaciones->count(),
            'vacanteFiltrada' => $this->nombreVacanteFiltrada($filtros['vacante_id'], $postulaciones),
            'estadosFiltrados' => $filtros['estado_ids'] ? $estados->pluck('nombre')->implode(', ') : null,
            'desde' => $filtros['fecha_desde'],
            'hasta' => $filtros['fecha_hasta'],
            'usuario' => $request->user()->nombre_corto,
            'generado' => now('America/Guatemala')->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->render();
        $this->agregarNumeroPagina($pdf);

        return $pdf->stream('reporte-postulaciones-por-estado.pdf');
    }
    private function validarFiltros(Request $request): array
    {
        $filtros = $request->validate([
            'vacante_id' => ['nullable', 'integer', 'exists:' . Vacante::class . ',id'],
            'estado_ids' => ['nullable', 'array'],
            'estado_ids.*' => ['required', 'integer', 'exists:' . PostulacionEstado::class . ',id'],
            'fecha_desde' => ['nullable', 'date_format:Y-m-d'],
            'fecha_hasta' => [
                'nullable',
                'date_format:Y-m-d',
                ...($request->filled('fecha_desde') ? ['after_or_equal:fecha_desde'] : []),
            ],
        ]);

        return [
            'vacante_id' => $filtros['vacante_id'] ?? null,
            'estado_ids' => array_values(array_unique(array_map('intval', $filtros['estado_ids'] ?? []))),
            'fecha_desde' => $filtros['fecha_desde'] ?? null,
            'fecha_hasta' => $filtros['fecha_hasta'] ?? null,
        ];
    }
    private function obtenerEstados(array $estadoIds): Collection
    {
        return PostulacionEstado::query()
            ->when($estadoIds, fn ($q) => $q->whereIn('id', $estadoIds))
            ->orderBy('id')
            ->get(['id', 'nombre']);
    }
    private function obtenerPostulaciones(array $filtros): Collection
    {
        ['vacante_id' => $vacanteId, 'estado_ids' => $estadoIds, 'fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta] = $filtros;

        return Postulacion::query()
            ->select(['id', 'candidato_id', 'vacante_id', 'estado_id', 'fecha_postulacion'])
            ->with([
                'candidato:id,primer_nombre,segundo_nombre,tercer_nombre,primer_apellido,segundo_apellido,correo_contacto',
                'vacante:id,nombre',
            ])
            ->when($vacanteId, fn ($q) => $q->where('vacante_id', $vacanteId))
            ->when($estadoIds, fn ($q) => $q->whereIn('estado_id', $estadoIds))
            ->when($fechaDesde, fn ($q) => $q->where('fecha_postulacion', '>=', $fechaDesde))
            ->when($fechaHasta, fn ($q) => $q->where('fecha_postulacion', '<', CarbonImmutable::parse($fechaHasta)->addDay()->toDateString()))
            ->orderBy('fecha_postulacion')
            ->orderBy('id')
            ->get();
    }
    private function agruparPorVacante(Collection $postulaciones, Collection $estados): Collection
    {
        return $postulaciones
            ->groupBy('vacante_id')
            ->map(fn (Collection $items) => $this->armarVacante($items, $estados))
            ->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }
    private function armarVacante(Collection $items, Collection $estados): array
    {
        $porEstado = $items->groupBy('estado_id');

        return [
            'nombre' => $items->first()->vacante?->nombre ?? 'Vacante sin nombre',
            'total' => $items->count(),
            'resumen' => $estados->map(fn ($e) => [
                'nombre' => $e->nombre,
                'cantidad' => $porEstado->get($e->id)?->count() ?? 0,
            ])->all(),
            'estados' => $estados
                ->filter(fn ($e) => $porEstado->has($e->id))
                ->map(fn ($e) => [
                    'nombre' => $e->nombre,
                    'postulaciones' => $porEstado[$e->id]->map(fn (Postulacion $p) => $this->armarPostulacion($p))->values()->all(),
                ])
                ->values()
                ->all(),
        ];
    }
    private function armarPostulacion(Postulacion $postulacion): array
    {
        return [
            'id' => $postulacion->id,
            'candidato' => $postulacion->candidato?->nombre_completo,
            'correo' => $postulacion->candidato?->correo_contacto,
            'fecha' => $postulacion->fecha_postulacion?->format('d/m/Y'),
        ];
    }
    private function nombreVacanteFiltrada(?int $vacanteId, Collection $postulaciones): ?string
    {
        if (! $vacanteId) {
            return null;
        }

        return $postulaciones->first()?->vacante?->nombre
            ?? Vacante::whereKey($vacanteId)->value('nombre');
    }
    private function agregarNumeroPagina($pdf): void
    {
        $dompdf = $pdf->getDomPDF();
        $lienzo = $dompdf->getCanvas();
        $fuente = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');

        $lienzo->page_text(
            $lienzo->get_width() - 118,
            $lienzo->get_height() - 26,
            'Página {PAGE_NUM} / {PAGE_COUNT}',
            $fuente,
            8,
            [0.32, 0.39, 0.46]
        );
    }
}
