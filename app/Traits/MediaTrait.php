<?php

namespace App\Traits;

trait MediaTrait
{

    public function guardarArchivo($model, $archivo, $coleccion, $user, $request)
    {
        $media = $model->addMedia($archivo)->toMediaCollection($coleccion);


        $media->update([
            "gerencia_id" => (int)$user->usuarioSigua->gerencia_id,
            "unidad_id" => (int)$user->usuarioSigua->unidad_id,
            "region_id" => (int)$user->usuarioSigua->region_id,
            "user_id" => (int)$user->id,
            "tipo_id" => $request->tipo_id ?? null,
            "clase_id" => $request->clase_id ?? null,
            "asociado_id" => $request->asociado_id ?? null,
        ]);

        return $media;
    }

}
