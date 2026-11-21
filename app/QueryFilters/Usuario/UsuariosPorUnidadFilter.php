<?php

namespace App\QueryFilters\Usuario;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\Filters\Filter;

class UsuariosPorUnidadFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $usuario = Auth::user();

        if ($usuario && $usuario->unidad_id) {
            $query->where('estado', 'Activo')->where('unidad_id', $usuario->unidad_id);
        } else {
            // si no tiene unidad asociada → no devuelve nada
            $query->whereRaw('1=0');
        }

        return $query;
    }
}
