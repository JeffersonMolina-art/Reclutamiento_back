<?php

namespace App\QueryFilters\Unidad;

use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\Filters\Filter;

class UnidadUsuario implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $usuario = Auth::user();

        if ($usuario) {
            // buscar todas las regiones donde el usuario sea jefe
            $regionIds = Region::where('jefe_id', $usuario->id)->pluck('id');

            if ($regionIds->isNotEmpty()) {
                $query->whereIn('regiones_id', $regionIds)
                    ->where('codigo', '<>', '00'); // 🚫 excluir unidades con código 00
            }
        }

        return $query;
    }


}
