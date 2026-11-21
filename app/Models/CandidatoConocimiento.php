<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidatoConocimiento extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.candidato_conocimientos';

    protected $fillable = ['candidato_id', 'descripcion', 'nivel'];
}
