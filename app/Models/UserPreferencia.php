<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreferencia extends Model
{

    protected $table = 'reclutamiento.user_preferencias';

    protected $fillable = [
        'ancho_contenido_aplicacion',
        'aspecto',
        'color_primario_tema_light',
        'color_oscurecimiento_primario_tema_light',
        'color_primario_tema_dark',
        'color_oscurecimiento_primario_tema_dark',
        'desenfoque_habilitado_barra_navegacion',
        'navBar_loyout',
        'es_navBar_vertical_semioscura',
        'esta_navBar_vertical_habilitada',
        'esta_navBar_vertical_colapsada',
        'tema',
        'tema_color',
        'navBar_vertical_tipo',
        'navBar_horizontal_tipo',
        'direccion',
        'user_id',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
