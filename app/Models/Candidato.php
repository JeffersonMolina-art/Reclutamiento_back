<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidato extends Model
{
    use SoftDeletes;

    protected $table = 'reclutamiento.candidato';

    protected $fillable = [
        'user_id',
        'dpi',
        'primer_nombre',
        'segundo_nombre',
        'tercer_nombre',
        'primer_apellido',
        'segundo_apellido',
        'correo_contacto',
        'telefono_contacto',
        'fecha_nacimiento',
        'expectativa_salarial',
    ];

    protected $casts = ['fecha_nacimiento' => 'date'];
}
