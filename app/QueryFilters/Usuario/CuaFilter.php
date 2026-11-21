<?php

namespace App\QueryFilters\Usuario;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class CuaFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (is_array($value)) {
            $query->whereHas('asociado', function (Builder $q) use ($value) {
                $q->whereIn('codigo_cliente_unico', $value);
            });
        } else {
            $query->whereHas('asociado', function (Builder $q) use ($value) {
                $q->where('codigo_cliente_unico', $value);
            });
        }

        return $query;
    }


}
