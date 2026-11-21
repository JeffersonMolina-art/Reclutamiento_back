<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        //traer los campos del modelo
        return [
            'ancho_contenido_aplicacion' => $this->ancho_contenido_aplicacion,
            'aspecto' => $this->aspecto,
            'color_primario_tema_light' => $this->color_primario_tema_light,
            'color_oscurecimiento_primario_tema_light' => $this->color_oscurecimiento_primario_tema_light,
            'color_primario_tema_dark' => $this->color_primario_tema_dark,
            'color_oscurecimiento_primario_tema_dark' => $this->color_oscurecimiento_primario_tema_dark,
            'desenfoque_habilitado_barra_navegacion' => $this->desenfoque_habilitado_barra_navegacion,
            'navBar_loyout' => $this->navBar_loyout,
            'es_navBar_vertical_semioscura' => $this->es_navBar_vertical_semioscura,
            'esta_navBar_vertical_habilitada' => $this->esta_navBar_vertical_habilitada,
            'esta_navBar_vertical_colapsada' => $this->esta_navBar_vertical_colapsada,
            'tema' => $this->tema,
            'tema_color' => $this->tema_color,
            'navBar_vertical_tipo' => $this->navBar_vertical_tipo,
            'navBar_horizontal_tipo' => $this->navBar_horizontal_tipo,
            'direccion' => $this->direccion,
        ];

    }
}
