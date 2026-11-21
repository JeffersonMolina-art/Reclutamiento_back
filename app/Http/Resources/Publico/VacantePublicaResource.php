<?php

namespace App\Http\Resources\Publico;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Vacante */
class VacantePublicaResource extends JsonResource
{
    /**
     * Solo datos aptos para exposición pública (sin usuarios ni ids internos de estado).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'puesto' => $this->puesto?->nombre,
            'sede' => $this->sede?->nombre,
            'cantidad_plazas' => $this->cantidad_plazas,
            'modalidad_trabajo' => $this->modalidad_trabajo,
            'salario_minimo' => $this->salario_minimo,
            'salario_maximo' => $this->salario_maximo,
            'fecha_publicacion' => $this->fecha_publicacion?->toDateString(),
            'caracteristicas' => $this->caracteristicas->map(fn ($c) => [
                'nombre' => $c->nombre,
                'valor' => $c->valor,
            ])->values(),
            'requisitos' => $this->requisitos->map(fn ($r) => [
                'nombre' => $r->nombre,
                'es_obligatorio' => $r->es_obligatorio,
            ])->values(),
        ];
    }
}
