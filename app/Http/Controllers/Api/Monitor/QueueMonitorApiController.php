<?php

namespace App\Http\Controllers\Api\Monitor;

use App\Http\Controllers\AppBaseController;
use App\Jobs\Pruebas\TestQueueMonitorJob;
use App\Models\Monitor\FailedJob;
use App\Models\Monitor\QueueMonitor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class QueueMonitorApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:listar jobs', only: ['index', 'show', 'jobNames', 'resumen']),
            new Middleware('abilities:reintentar job', only: ['reintentarJobs']),
            new Middleware('abilities:borrar job', only: ['destroy']),
            new Middleware('abilities:borrar todos jobs', only: ['destroyAll']),
        ];
    }

        /**
         * Listado de jobs monitoreados
         * GET /queue_monitor
         */
    public function index(): JsonResponse
    {
        $monitores = QueryBuilder::for(
            QueueMonitor::query()->ultimoPorJob()
        )
            ->conAtributoAdicional([
                'status_texto',
                'total_jobs',
            ])
            ->allowedFilters(
                'job_uuid',
                'name',
                'queue',
                'status',
                AllowedFilter::scope('porFechaFinalizacion', 'porFechaFinalizacion'),
            )
            ->allowedSorts(
                'id',
                'status',
                'started_at',
                'finished_at',
            )
            ->defaultSort('-id')
            ->jsonPaginate(request('page.size') ?? 10);


        return $this->sendResponse($monitores, 'Queue monitor obtenido con éxito.');
    }
    /**
     * Detalle de un job
     * GET /queue_monitor/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $base = QueueMonitor::findOrFail($id);

            $limit = (int) $request->get('limit', 3);

            $query = QueueMonitor::query()
                ->where('job_uuid', $base->job_uuid)
                ->orderByDesc('id');

            $total = $query->count();

            $historial = $query
                ->limit($limit)
                ->get();

            return $this->sendResponse(
                [
                    'items' => $historial,
                    'total' => $total,
                    'has_more' => $limit < $total,
                ],
                'Historial del job obtenido con éxito.'
            );
        } catch (\Throwable $e) {
            return $this->sendError(manejarException($e));
        }
    }
    public function reintentarJobs(int $id): JsonResponse
    {
        $monitor = QueueMonitor::find($id);

        if (!$monitor) {
            return $this->sendError('No se encontró el job en queue_monitor.', 404);
        }

        if ((int) $monitor->status !== 2) {
            return $this->sendError('El job no está en estado fallido.', 422);
        }

        $failedJob = $monitor->job_uuid;

        if (!$failedJob) {
            return $this->sendError(
                'El job no existe en failed_jobs. Laravel no tiene nada que reintentar.',
                422
            );
        }
        Artisan::call('queue:retry ' . $failedJob);

            return $this->sendResponse(null, 'Job reenviado a la cola correctamente.');
        }
    public function jobNames(): JsonResponse
    {
        try {
            $names = QueryBuilder::for(QueueMonitor::class)
                ->select('name')
                ->whereNotNull('name')
                ->distinct()
                ->allowedFilters(
                    'name',
                )
                ->defaultSort('name')
                ->jsonPaginate(request('page.size') ?? 10);

            // Transformar SOLO el data interno para evitar accessors
            $names->getCollection()->transform(fn ($item) => [
                'name' => $item->name,
            ]);

            return $this->sendResponse(
                $names,
                'Nombres de jobs obtenidos con éxito.'
            );
        } catch (\Throwable $e) {
            return $this->sendError(manejarException($e));
        }
    }
    public function resumen(Request $request): JsonResponse
    {
        try {
            $query = QueueMonitor::query();

            if ($request->filled('filter.status')) {
                $query->where('status', $request->input('filter.status'));
            }

            if ($request->filled('filter.name')) {
                $query->where('name', $request->input('filter.name'));
            }

            if ($request->filled('filter.porFechaFinalizacion')) {
                $query->porFechaFinalizacion(
                    $request->input('filter.porFechaFinalizacion')
                );
            }

            $jobs = $query->get(['status','started_at_exact', 'finished_at_exact']);
            $totalJobs = $jobs
                ->count();

            $totalMs = $jobs
                ->filter(fn ($job) =>
                    !empty($job->started_at_exact) &&
                    !empty($job->finished_at_exact)
                )
                ->sum(fn ($job) =>
                Carbon::parse($job->started_at_exact)
                    ->diffInMilliseconds(Carbon::parse($job->finished_at_exact))
                );

            $promedioMs = $totalJobs > 0 ? $totalMs / $totalJobs : 0;

            $totalTareasUnicas = $query
                ->ultimoPorJob()
                ->count();


            $estados = (clone $query)
                ->ultimoPorJob()
                ->selectRaw('status, COUNT(*) as cantidad')
                ->groupBy('status')
                ->get()
                ->map(fn ($item) => [
                    'status' => (int) $item->status,
                    'cantidad' => (int) $item->cantidad,
                ]);


            return $this->sendResponse([
                'total_tareas_unicas' => $totalTareasUnicas,
                'total_jobs'      => $totalJobs,
                'tiempo_total' => formatearMs($totalMs),
                'tiempo_promedio'   => formatearMs(round($promedioMs, 2)),
                'estados' => $estados,
            ], 'Resumen Monitor');

        } catch (\Throwable $e) {
            return $this->sendError(manejarException($e));
        }
    }
    public function destroy(int $id): JsonResponse
    {
        try {
            $job = QueueMonitor::findOrFail($id);

            if ($job->status === QueueMonitor::FALLIDO && $job->job_uuid) {
                FailedJob::where('uuid', $job->job_uuid)->delete();
            }

            QueueMonitor::where('job_uuid', $job->job_uuid)->delete();

            return $this->sendSuccess('Job eliminado correctamente.');
        } catch (\Throwable $e) {
            return $this->sendError(manejarException($e));
        }
    }

    public function destroyAll(): JsonResponse
    {
        try {
            FailedJob::query()->delete();
            QueueMonitor::query()->delete();

            return $this->sendSuccess('Todos los jobs fueron eliminados correctamente.');
        } catch (\Throwable $e) {
            return $this->sendError(manejarException($e));
        }
    }

    public function ejecutar(Request $request): JsonResponse
    {
        $request->validate([
            'modo' => 'required|string'
        ]);

        $modo = $request->modo;

        // Estados reales
        if (in_array($modo, ['exitoso', 'fallido'])) {

            TestQueueMonitorJob::dispatch($modo);

        } else {

            // Estados simulados manualmente
            $estadoMap = [
                'en_cola' => 4,
                'en_ejecucion' => 0,
                'expirado' => 3,
            ];

            $jobId = DB::table('reclutamiento.jobs')->insertGetId([
                'queue' => 'default',
                'payload' => json_encode([
                    'uuid' => (string) Str::uuid(),
                    'displayName' => 'TestQueueMonitorJob',
                    'job' => 'App\Jobs\Pruebas\TestQueueMonitorJob',
                    'data' => [],
                ]),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => now()->timestamp,
                'created_at' => now()->timestamp,
            ]);

            QueueMonitor::create([
                'job_id' => $jobId,
                'job_uuid' => (string) Str::uuid(),
                'name' => 'TestQueueMonitorJob',
                'queue' => 'default',
                'status' => match ($modo) {
                    'en_cola' => 4,
                    'en_ejecucion' => 0,
                    'expirado' => 3,
                    default => 4,
                },
                'progress' => $modo === 'en_ejecucion' ? 50 : 0,
                'started_at' => now(),
                'started_at_exact' => now(),
                'finished_at' => $modo === 'en_cola' ? null : now(),
                'finished_at_exact' => $modo === 'en_cola' ? null : now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Job de prueba generado correctamente'
        ]);
    }

}
