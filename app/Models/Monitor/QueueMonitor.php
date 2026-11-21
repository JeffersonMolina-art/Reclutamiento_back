<?php

namespace App\Models\Monitor;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use romanzipp\QueueMonitor\Models\Monitor as BaseQueueMonitor;

/**
 * @property int $id
 * @property string|null $job_uuid
 * @property string $job_id
 * @property string|null $name
 * @property string|null $queue
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $queued_at
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property string|null $started_at_exact
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property string|null $finished_at_exact
 * @property int $attempt
 * @property bool $retried
 * @property int|null $progress
 * @property string|null $exception
 * @property string|null $exception_message
 * @property string|null $exception_class
 * @property string|null $data
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereAttempt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereExceptionClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereExceptionMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereFinishedAtExact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereJobUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereQueuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereRetried($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereStartedAtExact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueMonitor whereStatus($value)
 * @mixin \Eloquent
 */
class QueueMonitor extends BaseQueueMonitor
{


    use HasFactory;

    protected $table = 'reclutamiento.queue_monitor';

    const EN_EJECUCION = 0;
    const EXITOSO = 1;
    const FALLIDO = 2;
    const EXPIRADO = 3;
    const EN_COLA = 4;


    protected $fillable =
        [
            'job_uuid',
            'job_id',
            'name',
            'queue',
            'status',
            'queued_at',
            'started_at',
            'started_at_exact',
            'finished_at',
            'finished_at_exact',
            'attempt',
            'retried',
            'progress',
            'exception',
            'exception_message',
            'exception_class',
            'data'
        ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'job_uuid' => 'string',
        'job_id' => 'string',
        'name' => 'string',
        'queue' => 'string',
        'status' => 'integer',
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'started_at_exact' => 'string',
        'finished_at' => 'datetime',
        'finished_at_exact' => 'string',
        'attempt' => 'integer',
        'retried' => 'boolean',
        'progress' => 'integer',
        'exception' => 'string',
        'exception_message' => 'string',
        'exception_class' => 'string',
        'data' => 'string',
    ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
    [
    'job_uuid' => 'nullable|string',
    'job_id' => 'required|string|max:510',
    'name' => 'nullable|string|max:510',
    'queue' => 'nullable|string|max:510',
    'status' => 'required|integer',
    'queued_at' => 'nullable|date',
    'started_at' => 'nullable|date',
    'started_at_exact' => 'nullable|string|max:510',
    'finished_at' => 'nullable|date',
    'finished_at_exact' => 'nullable|string|max:510',
    'attempt' => 'required|integer',
    'retried' => 'required|boolean',
    'progress' => 'nullable|integer',
    'exception' => 'nullable|string',
    'exception_message' => 'nullable|string',
    'exception_class' => 'nullable|string',
    'data' => 'nullable|string',
];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public static $atributosAdicionalesPorScope = [];



    public function scopeConAtributoAdicional($query, $atributoAdicionalNombre)
    {

        if (is_array($atributoAdicionalNombre)) {
            self::$atributosAdicionalesPorScope = array_merge(self::$atributosAdicionalesPorScope, $atributoAdicionalNombre);
            return $query;
        } else {
            self::$atributosAdicionalesPorScope[] = $atributoAdicionalNombre;
        }

        return $query;

    }
    protected function getArrayableAppends(): array
    {
        if (count(self::$atributosAdicionalesPorScope) > 0) {
            $this->appends = array_merge($this->appends, self::$atributosAdicionalesPorScope);
            return $this->appends;
        }


        return parent::getArrayableAppends();
    }

    public function getStatusTextoAttribute(): string
    {
        return match ($this->status) {
            0 => 'En ejecución',
            1 => 'Exitoso',
            2 => 'Fallido',
            3 => 'Expirado',
            4 => 'En cola',
            default => 'Desconocido',
        };
    }
    public function scopePorFechaFinalizacion(Builder $query, $fechas): Builder
    {
        if (!$fechas) return $query;
        // Normalizar input
        if (is_string($fechas)) {
            if (str_contains($fechas, ',')) {
                $fechas = explode(',', $fechas);
            } elseif (str_contains($fechas, ' to ')) {
                $fechas = explode(' to ', $fechas);
            } else {
                return $query;
            }
        }

        if (!is_array($fechas) || count($fechas) !== 2) return $query;

        [$inicio, $fin] = $fechas;


        if (!$inicio || !$fin) return $query;

        $inicio = Carbon::createFromFormat('d-m-Y', trim($inicio))
            ->startOfDay()
            ->utc();

        $fin = Carbon::createFromFormat('d-m-Y', trim($fin))
            ->endOfDay()
            ->utc();

        return $query->whereBetween('finished_at', [$inicio, $fin]);
    }
    public function scopeUltimoPorJob(Builder $query): Builder
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('reclutamiento.queue_monitor')
                ->whereNotNull('job_uuid')
                ->groupBy('job_uuid');
        });
    }
    public function getTotalJobsAttribute(): int
    {
        if (!$this->job_uuid) {
            return 0;
        }

        return self::query()
            ->where('job_uuid', $this->job_uuid)
            ->count();
    }


}
