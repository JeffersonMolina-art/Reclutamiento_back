<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostulacionEstado extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.postulacion_estado';

    protected $fillable = ['nombre', 'codigo', 'bandeja_id'];
}
