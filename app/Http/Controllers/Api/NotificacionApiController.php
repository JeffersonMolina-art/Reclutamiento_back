<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Dflydev\DotAccessData\Data;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateNotificacionApiRequest;
use App\Http\Requests\Api\UpdateNotificacionApiRequest;
use App\Models\Notificacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Carbon\Carbon;


/**
 * Class NotificacionApiController
 */
class NotificacionApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:ver notificacions', only: ['index', 'show']),
            new Middleware('abilities:crear notificacions', only: ['store']),
            new Middleware('abilities:editar notificacions', only: ['update']),
            new Middleware('abilities:eliminar notificacions', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Notifications.
     * GET|HEAD /notificacions
     */
    public function index(): JsonResponse
    {

        $maxResults = request('page.size') ? request('page.size') : 10 ;

        $notificaciones = QueryBuilder::for(Notificacion::class)
            ->allowedFilters('type', 'notifiable_type', 'notifiable_id', 'titulo', 'read_at')
            ->allowedFields('id', 'type', 'notifiable_type', 'notifiable_id', 'titulo', 'data', 'read_at', 'created_at')
            ->allowedSorts('id', 'type', 'titulo', 'notifiable_id', 'notifiable_type', 'created_at')
            ->whereNull('deleted_at')
            ->jsonPaginate($maxResults);

        if($notificaciones->isEmpty()){
            return $this->sendError('Notificaciones no encontradas.', 404);
        }

        return $this->sendResponse($notificaciones->toArray(), 'Listado de notificaciones.');
    }

    public function marcarLeida(Request $request, Notificacion $notificacion): JsonResponse
    {

        $notificacion = $request->user()->notifications()->find($notificacion->id);

        if (!$notificacion) {
            return $this->sendError('Notificación no encontrada.', 404);
        }

        if (!$notificacion->read_at) {
            $notificacion->markAsRead();
        }

        return $this->sendSuccess('Notificación marcada como leída.');
    }

    public function marcarleidoTodos(Request $request): JsonResponse
    {
        $notificaciones = $request->user()->notificacionesSinLeer()->get();
        foreach ($notificaciones as $notificacion) {
            $notificacion->markAsRead();
        }
        return $this->sendResponse(null, 'Todas las notificaciones marcadas como leídas.');
    }

    /**
     * Store a newly created Notificacion in storage.
     * POST /notificacions
     */
    public function store(CreateNotificacionApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $notificacions = Notificacion::create($input);

        return $this->sendResponse($notificacions->toArray(), 'Notificacion creado con éxito.');
    }

    /**
     * Display the specified Notificacion.
     * GET|HEAD /notificacions/{id}
     */
    public function show(Notificacion $notificacion)
    {
        return $this->sendResponse($notificacion->toArray(), 'Notificacion recuperado con éxito.');
    }

    /**
    * Update the specified Notificacion in storage.
    * PUT/PATCH /notificacions/{id}
    */
    public function update(UpdateNotificacionApiRequest $request, $id): JsonResponse
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update($request->validated());
        return $this->sendResponse($notificacion, 'Notificacion actualizado con éxito.');
    }

    /**
    * Remove the specified Notificacion from storage.
    * DELETE /notificacions/{id}
    */
    public function destroy(Notificacion $notificacion): JsonResponse
    {
        $notificacion->delete();
        return $this->sendResponse(null, 'Notificacion eliminado con éxito.');
    }

    /**
     * Listar las notificaciones no leídas del usuario autenticado.
     * @param  Request  $request
     * @return JsonResponse
     */
    public function obtenerNotificacionesNoLeidas(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 7);
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if ($fechaInicio) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        }

        if ($fechaFin) {
            $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        }

        $query = $request->user()->notificacionesSinLeer();

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $notificacionesNoLeidas = $query->paginate($limit);

        return $this->sendResponse($notificacionesNoLeidas, 'Listado de notificaciones no leídas.');
    }

    /**
     * Listar las notificaciones leídas del usuario autenticado.
     * @param  Request  $request
     * @return JsonResponse
     */
    public function obtenerNotificacionesLeidas(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 7);
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if ($fechaInicio) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        }

        if ($fechaFin) {
            $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        }
        $query = $request->user()->notificacionesLeidas();

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $notificacionesLeidas = $query->paginate($limit);

        return $this->sendResponse($notificacionesLeidas, 'Listado de notificaciones leídas.');
    }

    public function notificarDenegacionUbicacion(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required',
            'message' => 'required|string|max:255',
            'event' => 'required|string|max:255',
        ]);

        $user = \App\Models\User::findOrFail($data['user_id']);

        $usuario_jefe = $user->unidad->jefe;

        $usuario_jefe->notify(new \App\Notifications\NotificacionSistema($data['event'],$data['message']));

        return $this->sendResponse([], 'Notificación enviada al usuario.');
    }

    public function generarNotificacionesPrueba(Request $request): JsonResponse
    {
        $request->validate([
            'leidas' => 'nullable|integer|min:0',
            'no_leidas' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();
        $faker = \Faker\Factory::create('es_ES');

        // Generar notificaciones no leídas
        for ($i = 0; $i < $request->no_leidas; $i++) {
            $user->notifyNow(new \App\Notifications\NotificacionSistema(
                $faker->sentence(3),
                $faker->paragraph(1)
            ));
        }

        // Generar notificaciones leídas
        for ($i = 0; $i < $request->leidas; $i++) {
            $user->notifyNow(new \App\Notifications\NotificacionSistema(
                $faker->sentence(3),
                $faker->paragraph(1)
            ));

            // Marcar la última notificación creada como leída
            // Usamos ->first() sin ordenar para evitar el error de duplicación de columnas en SQL Server
            // ya que la relación notifications() ya tiene un orderBy por defecto en el Trait
            $notification = $user->notifications()->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return $this->sendSuccess('Notificaciones de prueba generadas correctamente.');
    }

}
