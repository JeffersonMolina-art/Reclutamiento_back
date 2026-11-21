<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class UserExterno extends Model
{
    use HasApiTokens, SoftDeletes;

    public const ESTADO_ACTIVO = 1;

    protected $table = 'reclutamiento.user_externos';

    protected $fillable = ['email', 'telefono', 'password', 'estado_id'];

    protected $hidden = ['password'];

    public function candidato(): HasOne
    {
        return $this->hasOne(Candidato::class, 'user_id');
    }
}
