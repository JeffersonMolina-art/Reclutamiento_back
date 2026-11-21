<?php

namespace App\QueryFilters\Usuario;

use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\Filters\Filter;

class UsuariosPorRegionFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $usuario = Auth::user();
        if ($value && $usuario) {
            $regionIds = Region::where('jefe_id', $usuario->id)->pluck('id');
            if ($regionIds->isNotEmpty()) {
                $query->whereIn('region_id', $regionIds);
            } else {
                $query->whereRaw('1=0');
            }
        }

        return $query;
    }

}
