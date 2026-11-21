<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacanteEstado extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.vacantes_estados';

    protected $fillable = ['nombre', 'codigo'];
}
