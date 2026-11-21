<?php

namespace App\Models;

use App\Traits\HasBitacora;
use App\Traits\HasFlujoPostulacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Postulacion extends Model
{
    use HasFlujoPostulacion, HasBitacora;

    public const ESTADO_INICIAL = 1;

    protected $table = 'reclutamiento.postulaciones';

    protected $fillable = [
        'vacante_id',
        'candidato_id',
        'estado_id',
        'fecha_postulacion',
        'fuente_reclutamiento',
    ];

    protected $casts = ['fecha_postulacion' => 'date'];

    public function scopeEnBandeja(Builder $query, string $codigo): Builder
    {
        return $query->whereHas('estado.bandeja', fn (Builder $q) => $q->where('codigo', $codigo));
    }
    public function scopeVisiblesPara(Builder $query, User $usuario): Builder
    {
        if ($usuario->hasAnyRole(Role::ROLES_ADMINISTRATIVOS)) {
            return $query;
        }

        return $query->whereHas('asignacion', fn (Builder $q) => $q->where('users_id', $usuario->id));
    }

    public function vacante(): BelongsTo
    {
        return $this->belongsTo(Vacante::class, 'vacante_id');
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(PostulacionEstado::class, 'estado_id');
    }

    public function asignacion(): HasOne
    {
        return $this->hasOne(PostulacionAsignacion::class, 'postulacion_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoPostulacion::class, 'postulacion_id');
    }
}
