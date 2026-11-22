<?php

    use App\Http\Controllers\Api\PostulacionApiController;
    use App\Http\Controllers\Api\VacanteApiController;
    use Illuminate\Support\Facades\Route;

    Route::prefix('reclutamiento')->name('reclutamiento.')->group(function () {
        Route::post('postulaciones/{postulacion}/asignar', [PostulacionApiController::class, 'asignar'])->name('postulaciones.asignar');
        Route::post('postulaciones/{postulacion}/comentarios', [PostulacionApiController::class, 'comentar'])->name('postulaciones.comentar');
        Route::get('postulaciones/{postulacion}/cv', [PostulacionApiController::class, 'cv'])->name('postulaciones.cv');
        Route::get('postulaciones/{postulacion}/archivos', [PostulacionApiController::class, 'listarArchivos'])->name('postulaciones.archivos.listar');
        Route::post('postulaciones/{postulacion}/archivos', [PostulacionApiController::class, 'subirArchivo'])->name('postulaciones.archivos.subir');
        Route::get('postulaciones/{postulacion}/archivos/{media}', [PostulacionApiController::class, 'descargarArchivo'])->name('postulaciones.archivos.descargar');
        Route::delete('postulaciones/{postulacion}/archivos/{media}', [PostulacionApiController::class, 'eliminarArchivo'])->name('postulaciones.archivos.eliminar');
        Route::apiResource('vacantes', VacanteApiController::class)->parameters(['vacantes' => 'vacante']);
        Route::apiResource('postulaciones', PostulacionApiController::class)->only(['index', 'show', 'update'])
            ->parameters(['postulaciones' => 'postulacion']);
    });
