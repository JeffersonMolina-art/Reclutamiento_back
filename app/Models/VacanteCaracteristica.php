<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacanteCaracteristica extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.vacante_caracteristicas';

    protected $fillable = ['vacante_id', 'nombre', 'valor'];
}
