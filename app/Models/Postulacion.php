<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Postulacion extends Model
{
    public const ESTADO_INICIAL = 1;

    protected $table = 'reclutamiento.postulaciones';

    protected $fillable = ['vacante_id', 'candidato_id', 'estado_id', 'fecha_postulacion', 'fuente_reclutamiento'];

    protected $casts = ['fecha_postulacion' => 'date'];

    public function vacante(): BelongsTo
    {
        return $this->belongsTo(Vacante::class, 'vacante_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(PostulacionEstado::class, 'estado_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoPostulacion::class, 'postulacion_id');
    }
}
