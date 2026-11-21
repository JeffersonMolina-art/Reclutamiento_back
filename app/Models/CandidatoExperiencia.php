<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidatoExperiencia extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.candidato_experiencias';

    protected $fillable = ['candidato_id', 'empresa', 'puesto', 'fecha_inicio', 'fecha_fin'];

    protected $casts = ['fecha_inicio' => 'date:Y-m-d', 'fecha_fin' => 'date:Y-m-d'];
}
