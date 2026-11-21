<?php

namespace App\Http\Controllers\Api\Configuraciones;
use App\Http\Controllers\AppBaseController;
use App\Models\ImagenConfiguracionTipo;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\ImagenConfiguracion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


/**
 * Class ImagenConfiguracionApiController
 */
class ImagenConfiguracionApiController extends AppbaseController
{


    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
        new Middleware('abilities:ver imagen-configuracions', only: ['index', 'show']),
        new Middleware('abilities:crear imagen-configuracions', only: ['store']),
        new Middleware('abilities:editar imagen-configuracions', only: ['update']),
        new Middleware('abilities:eliminar imagen-configuracions', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the Imagen_configuracion.
     * GET|HEAD /imagen-configuracions
     */
    public function index(Request $request): JsonResponse
    {
        $query = ImagenConfiguracion::query();

        if ($request->get('skip')) {
            $query->skip($request->get('skip'));
        }

        if ($request->get('limit')) {
            $query->limit($request->get('limit'));
        }

        $imagen_configuracions = $query->get();

        return $this->sendResponse($imagen_configuracions->toArray(), 'imagen-configuracions recuperados con éxito.');
    }

    /**
     * Store a newly created ImagenConfiguracion in storage.
     * POST /imagen-configuracions
     */
    public function store(Request $request): JsonResponse
    {
        // Obtener todos los archivos enviados
        $files = $request->allFiles();

        // Verificar que exista al menos un archivo
        if (empty($files)) {
            return $this->sendError('No se encontró ningún archivo en la petición.');
        }

        // Tomar la primera clave del array de archivos
        $key = array_keys($files)[0];

        // Verificar que el archivo se haya subido correctamente utilizando la clave
        if (!$request->hasFile($key)) {
            return $this->sendError('El archivo no se cargó correctamente.');
        }

        $file = $request->file($key);

        // Convertir los guiones bajos del nombre de la clave en espacios
        $nombreSinGuionesBajos = str_replace('_', ' ', $key);

        // Buscar el tipo de imagen utilizando el nombre formateado
        $tipo = ImagenConfiguracionTipo::where('nombre', 'like', '%' . $nombreSinGuionesBajos . '%')->first();

        if (!$tipo) {
            return $this->sendError('Tipo de imagen no encontrado.');
        }

        // Crear el registro de imagen en la base de datos
        $imagenConfiguracion = ImagenConfiguracion::create([
            'nombre'  => $file->getClientOriginalName(),
            'tipo_id' => $tipo->id
        ]);

        // Asociar el archivo a la colección de medios (usando Spatie Media Library)
        $media = $imagenConfiguracion->addMedia($file)
            ->toMediaCollection($tipo->nombre);


        return $this->sendResponse($media, 'Imagen guardada con éxito.');
    }



    /**
     * Display the specified ImagenConfiguracion.
     * GET|HEAD /imagen-configuracions/{id}
     */
    public function show(ImagenConfiguracion $imagenconfiguracion)
    {
        return $this->sendResponse($imagenconfiguracion->toArray(), 'ImagenConfiguracion recuperado con éxito.');
    }

    /**
     * Update the specified ImagenConfiguracion in storage.
     * PUT/PATCH /imagen-configuracions/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $imagenconfiguracion = ImagenConfiguracion::findOrFail($id);
        $imagenconfiguracion->update($request->validated());
        return $this->sendResponse($imagenconfiguracion, 'ImagenConfiguracion actualizado con éxito.');
    }

    /**
     * Remove the specified ImagenConfiguracion from storage.
     * DELETE /imagen-configuracions/{id}
     */
    public function destroy(string $id)
    {

        $media = Media::find($id);

        if (empty($media)) {
            return $this->sendError('Archivo no encontrado');
        }

        $media->delete();

        return $this->sendResponse([], 'Archivo eliminado!');
    }

    public function getImegenesActuales(): JSONResponse
    {
        $ultimosRegistros = ImagenConfiguracion::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('reclutamiento.imagen_configuracion')
                ->groupBy('tipo_id');
        })->get();

        $media = [];

        foreach ($ultimosRegistros as $registro) {
            $media[str_replace(' ', '_', $registro->tipo->nombre)] = $registro->getFirstMedia($registro->tipo->nombre);
        }


        return $this->sendResponse($media, 'Imagenes recuperadas con éxito.');

    }


    public function ObtenerImagenFondo()
    {
        $tipos = [
            'claro'  => ImagenConfiguracionTipo::FONDO_LOGIN_CLARO,
            'oscuro' => ImagenConfiguracionTipo::FONDO_LOGIN_OSCURO,
        ];

        $data = [];

        foreach ($tipos as $key => $tipoId) {
            $imagen = ImagenConfiguracion::where('tipo_id', $tipoId)
                ->orderBy('id', 'desc')
                ->first();

            if ($imagen) {
                $media = $imagen->getFirstMedia($imagen->tipo->nombre);
                if ($media) {
                    $data[$key] = [
                        'url'        => $media->getUrl(),
                        'collection' => $media->collection_name,
                        'tipo'       => $imagen->tipo->nombre,
                    ];
                }
            }
        }

        if (empty($data)) {
            return $this->sendError('No se encontró ninguna imagen de fondo.');
        }

        return $this->sendResponse($data, 'Imágenes de fondo recuperadas con éxito.');
    }



}
