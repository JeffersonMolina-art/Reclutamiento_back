<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeguimientoPostulacion extends Model
{
    protected $table = 'reclutamiento.seguimiento_postulacion';

    protected $fillable = [
        'comentario',
        'porcentaje',
        'postulacion_id',
        'estado_id',
        'user_id',
        'puntaje_fase',
        'estado_anterior_id',
        'fecha_cambio',
    ];

    protected $casts = ['fecha_cambio' => 'datetime'];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(PostulacionEstado::class, 'estado_id');
    }
}
