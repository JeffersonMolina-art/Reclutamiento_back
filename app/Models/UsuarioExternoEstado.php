<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioExternoEstado extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.usuario_externo_estados';

    protected $fillable = ['nombre', 'codigo'];
}
