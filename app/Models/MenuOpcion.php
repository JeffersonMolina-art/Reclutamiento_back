<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuOpcion extends Model
{
    //

    protected $table = 'reclutamiento.menu_opciones';

    protected $fillable = [
        'titulo',
        'ruta',
        'icono',
        'action',
        'subject',
        'parent_id',
        'titulo_seccion',
        'orden',
    ];

    public static $rules = [
        'titulo' => 'max:125 | string',
        'ruta' => 'string | nullable | max:120',
        'icono' => 'max:125',
        'action' => 'max:150',
        'subject' => 'max:150',
        'parent_id' => 'nullable | integer',
        'titulo_seccion' => 'string | nullable'
    ];

    public static $messages = [
        'titulo.max' => 'El campo titulo no puede tener mas de 125 caracteres',
        'ruta.max' => 'El campo ruta no puede tener mas de 120 caracteres',
        'icono.max' => 'El campo icono no puede tener mas de 125 caracteres',
        'action.max' => 'El campo action no puede tener mas de 150 caracteres',
        'subject.max' => 'El campo subject no puede tener mas de 150 caracteres',
        'parent_id.integer' => 'El campo parent_id debe ser un entero',
        'titulo_seccion.string' => 'El campo titulo_seccion debe ser una cadena de texto'
    ];

    public function children(): HasMany
    {
        return $this->hasMany(MenuOpcion::class, 'parent_id', 'id')
            ->with('children')
            ->orderBy('orden');
    }



}


