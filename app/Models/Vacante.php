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

    public const ESTADO_PUBLICADA = 'publicada';

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
        'fecha_publicacion' => 'date',
        'salario_minimo' => 'decimal:2',
        'salario_maximo' => 'decimal:2',
    ];

    public function scopePublicas(Builder $query): Builder
    {
        return $query
            ->whereHas('estado', fn (Builder $q) => $q->where('codigo', self::ESTADO_PUBLICADA))
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
}
