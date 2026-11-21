<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserExterno extends Model
{
    use SoftDeletes;

    public const ESTADO_ACTIVO = 1;

    protected $table = 'reclutamiento.user_externos';

    protected $fillable = ['email', 'telefono', 'password', 'estado_id'];

    protected $hidden = ['password'];
}
