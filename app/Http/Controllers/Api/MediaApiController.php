<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Media;
use App\Models\SIGUA\Asociado;
use App\Traits\MediaTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class MediaApiController extends AppBaseController
{
    use MediaTrait;

    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver documentos', only: ['index', 'show', 'obtenerArchivos']),
            new Middleware('abilities:crear documentos', only: ['store']),
            new Middleware('abilities:actualizar documentos', only: ['update']),
            new Middleware('abilities:eliminar documentos', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'model_type' => 'required',
            'model_id' => 'required',
            'clase_id' => 'required',
            'tipo_id' => 'required',
            'coleccion_name' => 'required',
        ]);

        $archivos = $request->allFiles();

        if (empty($archivos)) {
            return $this->sendError('No se ha enviado ningún archivo');
        }

        $modelClass = $request->input('model_type');
        $model = $modelClass::find($request->input('model_id'));

        $user = $request->user();

        if (!$model || !$user) {
            return $this->sendError('Modelo o usuario no encontrado');
        }

        $coleccion = $request->coleccion_name;
        $mediaGuardados = [];

        foreach ($archivos as $archivo) {
            if (is_array($archivo)) {
                foreach ($archivo as $subArchivo) {
                    $mediaGuardados[] = $this->guardarArchivo($model, $subArchivo, $coleccion, $user, $request);
                }
            } else {
                $mediaGuardados[] = $this->guardarArchivo($model, $archivo, $coleccion, $user, $request);
            }
        }

        return $this->sendResponse(
            collect($mediaGuardados)
                ->filter() // Elimina los null o false
                ->map(fn($item) => $item->toArray()),
            'Archivo guardado exitosamente'
        );

    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        /** @var Media $media */
        $asociado = Asociado::findOrFail($id);

        if($request->collection_name == null){
            return $this->sendError('No se ha enviado el nombre de la colección');
        }

        $media = Media::where('asociado_id', $asociado->id)
            ->where('collection_name', $request->collection_name)
            ->get()
            ?->last();

        return $this->sendResponse($media, 'Media recuperado con éxito');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $media = Media::find($id);

        if (!$media) {
            return $this->sendError('Archivo no encontrado');
        }

        $media->delete();

        return $this->sendSuccess('Archivo eliminado exitosamente');
    }

    public function obtenerArchivos(Request $request)
    {

        $request->validate([
            'asociado_id'       => 'required_without_all:model_type,model_id',
            'model_type'        => 'required_without:asociado_id',
            'model_id'          => 'required_without:asociado_id',
            'nombre_coleccion'    => 'required|string',
            'obtener_ultimo'    => 'required_without:obtener_todos|boolean',
            'obtener_todos'     => 'required_without:obtener_ultimo|boolean',
        ]);

        $asociado_id = $request->asociado_id;
        $coleccion = $request->nombre_coleccion;

        if($asociado_id) {
            $asociado = Asociado::withoutAppends()->find($asociado_id);
            if (!$asociado) {
                return $this->sendError('Asociado no encontrado');
            }

            $media = $asociado->getMedia($coleccion);

            if ($request->obtener_ultimo) {
                return $this->sendResponse($media->last(), 'Archivo encontrado');
            }
            return $this->sendResponse($media, 'Archivos encontrados');
        } else {
            $modelClass = $request->model_type;
            if (!class_exists($modelClass) || !$modelo = $modelClass::find($request->model_id)) {
                return $this->sendError('Modelo no encontrado');
            }

            $media = $modelo->getMedia($coleccion);

            if ($request->obtener_ultimo) {
                return $this->sendResponse($media->last(), 'Archivo encontrado');
            }
            return $this->sendResponse($media, 'Archivos encontrados');
        }
    }
}
