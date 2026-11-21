<?php

namespace App\Models;

use App\Traits\HasBitacora;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacante extends Model
{
    use SoftDeletes, HasBitacora;

    public const ESTADO_PUBLICADA = 2;

    protected array $bitacoraEtiquetas = [
        'estado_id' => 'Estado',
        'puesto_id' => 'Puesto',
        'sede_id' => 'Sede',
        'nombre' => 'Nombre',
        'descripcion' => 'Descripción',
        'cantidad_plazas' => 'Cantidad de plazas',
        'user_id' => 'Creado por',
        'fecha_publicacion' => 'Fecha de publicación',
        'user_encargado' => 'Encargado',
        'modalidad_trabajo' => 'Modalidad de trabajo',
        'salario_minimo' => 'Salario mínimo',
        'salario_maximo' => 'Salario máximo',
    ];

    protected $table = 'reclutamiento.vacantes';

    protected $fillable = [
        'estado_id',
        'puesto_id',
        'sede_id',
        'nombre',
        'descripcion',
        'cantidad_plazas',
        'user_id',
        'fecha_publicacion',
        'user_encargado',
        'modalidad_trabajo',
        'salario_minimo',
        'salario_maximo',
    ];

    protected $casts = [
        'id' => 'integer',
        'estado_id' => 'integer',
        'puesto_id' => 'integer',
        'sede_id' => 'integer',
        'user_id' => 'integer',
        'user_encargado' => 'integer',
        'cantidad_plazas' => 'integer',
        'fecha_publicacion' => 'date',
        'salario_minimo' => 'decimal:2',
        'salario_maximo' => 'decimal:2',
    ];

    protected $appends = [
        'estado_nombre',
        'puesto_nombre',
        'sede_nombre',
        'encargado_nombre',
        'rango_salarial',
    ];

    public function scopePublicas(Builder $query): Builder
    {
        return $query
            ->whereHas('estado', fn (Builder $q) => $q->where('estado_id', self::ESTADO_PUBLICADA))
            ->whereNotNull('fecha_publicacion')
            ->whereDate('fecha_publicacion', '<=', now());
    }

    public function scopeSinPostulacionDe(Builder $query, int $candidatoId): Builder
    {
        return $query->whereDoesntHave('postulaciones', fn (Builder $q) => $q->where('candidato_id', $candidatoId));
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class, 'vacante_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(VacanteEstado::class, 'estado_id');
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function caracteristicas(): HasMany
    {
        return $this->hasMany(VacanteCaracteristica::class, 'vacante_id');
    }

    public function requisitos(): HasMany
    {
        return $this->hasMany(VacanteRequisito::class, 'vacante_id');
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_encargado');
    }

    public function getEstadoNombreAttribute(): ?string
    {
        return $this->estado?->nombre;
    }

    public function getPuestoNombreAttribute(): ?string
    {
        return $this->puesto?->nombre;
    }

    public function getSedeNombreAttribute(): ?string
    {
        return $this->sede?->nombre;
    }

    public function getEncargadoNombreAttribute(): ?string
    {
        return $this->encargado?->nombre_completo;
    }

    public function getRangoSalarialAttribute(): ?string
    {
        if ($this->salario_minimo === null || $this->salario_maximo === null) {
            return null;
        }

        $minimo = number_format((float) $this->salario_minimo, 2);
        $maximo = number_format((float) $this->salario_maximo, 2);

        return "Q {$minimo} – Q {$maximo}";
    }
}
