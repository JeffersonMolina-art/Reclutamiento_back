<?php


use App\Http\Controllers\Api\Configuraciones\ConfiguracionApiController;
use App\Http\Controllers\Api\Configuraciones\ImagenConfiguracionApiController;
use App\Http\Controllers\Api\Configuraciones\LogViewerApiController;
use App\Http\Controllers\Api\Configuraciones\UserFavoritoApiController;
use App\Http\Controllers\Api\Monitor\QueueMonitorApiController;
use Illuminate\Support\Facades\Route;

Route::get('configuracions/obtenerInfoPanel', [ConfiguracionApiController::class, 'obtenerInfoPanel'])->name('configuracions.obtenerInfoPanel');
//
Route::apiResource('user-favoritos', UserFavoritoApiController::class);
Route::prefix('user-favoritos')->group(function () {
    Route::get('/de/usuario', [UserFavoritoApiController::class, 'obtenerFavoritosDeUser']);
});

Route::prefix('configuraciones')->name('configuraciones.')->group(function () {
    Route::apiResource('imagen/configuracions', ImagenConfiguracionApiController::class);
    Route::get('imagen/getActuales', [ImagenConfiguracionApiController::class, 'getImegenesActuales']);

});

Route::prefix('configuracion')->group(function () {
    Route::prefix('log-viewer')->name('log-viewer.')->group(function () {
        Route::get('/menu', [LogViewerApiController::class, 'menu'])->name('menu');

        Route::get('/logs/{date}/download', [LogViewerApiController::class, 'download'])->name('logs.download');
        Route::get('/logs/{date}', [LogViewerApiController::class, 'show'])->name('logs.show');

        Route::delete('/logs/delete', [LogViewerApiController::class, 'delete'])->name('logs.delete');
        // BORRAR ANTES DE SUBIR A PRODUCCION
        Route::post('/logs/generarPrueba', [LogViewerApiController::class, 'generar'])->name('logs.generarPrueba');
    });

    Route::prefix('monitor')->name('monitor.')->group(function () {
        Route::get('jobs', [QueueMonitorApiController::class, 'index'])
            ->name('jobs.index');
        Route::get('jobs/show/{id}', [QueueMonitorApiController::class, 'show'])
            ->name('jobs.fallido.show');
        Route::post('/jobs/fallido/{id}/reintentar', [QueueMonitorApiController::class, 'reintentarJobs'])
            ->name('jobs.fallido.retry');
        Route::get('/jobs/estadisticas', [QueueMonitorApiController::class, 'resumen'])
            ->name('jobs.stats');
        Route::get('/jobs/names', [QueueMonitorApiController::class, 'jobNames'])
            ->name('jobs.names');
        Route::delete('/job/eliminar/{id}', [QueueMonitorApiController::class, 'destroy'])
            ->name('job.destroy');
        Route::delete('/jobs/eliminar/todos', [QueueMonitorApiController::class, 'destroyAll'])
            ->name('jobs.destroyAll');
        Route::post('/jobs/test', [QueueMonitorApiController::class, 'ejecutar'])
            ->name('jobs.test');
    });
});

Route::apiResource('configuracions', ConfiguracionApiController::class);




