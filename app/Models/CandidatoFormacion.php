<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidatoFormacion extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.candidato_formacion';

    protected $fillable = ['candidato_id', 'nombre', 'titulo_carrera', 'fecha_inicio', 'fecha_fin'];

    protected $casts = ['fecha_inicio' => 'date:Y-m-d', 'fecha_fin' => 'date:Y-m-d'];
}
