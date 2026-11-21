<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //traer los campos del modelo
        return [
            'id' => $this->id,
            'usuario_dominio' => $this->usuario_dominio,
            'email' => $this->email,
            'nombre' => $this->nombre,
            'apellido'=> $this->apellido,
            'estado'=> $this->estado,
            'puesto_id'=> $this->puesto_id,
            'region_id'=> $this->region_id,
            'gerencia_id'=> $this->gerencia_id,
            'unidad_id'=> $this->unidad_id,
            'created-at' => $this->created_at?->toAtomString(),
            'updated-at' => $this->updated_at?->toAtomString(),
        ];

    }
}
