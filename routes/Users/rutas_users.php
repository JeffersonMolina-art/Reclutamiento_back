<?php


use App\Http\Controllers\Api\NotificacionApiController;
use App\Http\Controllers\Api\PermisoApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\UserApicontroller;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'users' => UserApiController::class,
//    'permisos' => PermisoApiController::class,
]);

Route::group(['prefix' => 'permisos', 'as' => 'permisos.'], function () {
    Route::get('/{permiso}/roles', [PermisoApiController::class, 'roles']);
    Route::get('/sujetos', [PermisoApiController::class, 'sujetos'])->name('sujetos');
});
Route::apiResource('permisos', PermisoApiController::class);


Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
//    Route::get('/search', [RoleApiController::class, 'search'])->name('search');
    Route::get('/{role}/permisos', [RoleApiController::class, 'permisos']);
});
Route::apiResource('roles', RoleApiController::class);

Route::get('/data/all', [UserAPIController::class, 'getAllData'])->name('data.all');

require __DIR__ . '/user_preferencia.php';


Route::group(['prefix' => 'usuarios', 'as' => 'usuarios.'], function () {
    Route::put('/restablecer/contrasena/{user}', [UserApiController::class, 'restablecerContrasena'])->name('restablecerContrasena');
    Route::put('/devLoginAsUser', [UserAPIController::class, 'devLoginAsUser'])->name('devLoginAsUser');

    Route::apiResource('notificacions', NotificacionApiController::class);

    Route::group(['prefix' => '/notificacions', 'as' => 'notificacions.'], function () {

        //notificar al jefe agencia que denego la ubicacion
        Route::post('/denegacion/ubicacion', [NotificacionApiController::class, 'notificarDenegacionUbicacion'])
            ->name('notificarDenegacionUbicacion');

        Route::put('/marcar/leida/{notificacion}', [NotificacionApiController::class, 'marcarLeida']);

        Route::post('/marcarleidoTodos', [NotificacionApiController::class, 'marcarleidoTodos'])->name('marcarleidoTodos');

        Route::get('/obtener/noLeidas', [NotificacionApiController::class, 'obtenerNotificacionesNoLeidas']);

        Route::get('/obtener/leidas', [NotificacionApiController::class, 'obtenerNotificacionesLeidas']);

        // SOLO DISPONIBLE EN ENTORNO LOCAL
        Route::post('/generar/prueba', [NotificacionApiController::class, 'generarNotificacionesPrueba']);

    });



    Route::post('/actualizar/foto/perfil', [UserApiController::class, 'actualizarFotoPerfil']);


});
