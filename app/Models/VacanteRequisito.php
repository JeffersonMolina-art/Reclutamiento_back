<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacanteRequisito extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.vacante_requisitos';

    protected $fillable = ['vacante_id', 'nombre', 'es_obligatorio'];

    protected $casts = ['es_obligatorio' => 'boolean'];
}
