<?php

    use App\Http\Controllers\Api\CandidatoApiController;
    use App\Http\Controllers\Api\EntrevistaApiController;
    use App\Http\Controllers\Api\OfertaApiController;
    use App\Http\Controllers\Api\PostulacionApiController;
    use App\Http\Controllers\Api\VacanteApiController;
    use Illuminate\Support\Facades\Route;

    Route::prefix('reclutamiento')->name('reclutamiento.')->group(function () {
        Route::get('candidatos', [CandidatoApiController::class, 'index'])->name('candidatos.index');
        Route::get('candidatos/{candidato}/postulaciones', [CandidatoApiController::class, 'historial'])->name('candidatos.historial');
        Route::post('candidatos', [CandidatoApiController::class, 'store'])->name('candidatos.store');
        Route::get('postulaciones/historial', [PostulacionApiController::class, 'historial'])->name('postulaciones.historial');
        Route::post('postulaciones/{postulacion}/asignar', [PostulacionApiController::class, 'asignar'])->name('postulaciones.asignar');
        Route::post('postulaciones/{postulacion}/comentarios', [PostulacionApiController::class, 'comentar'])->name('postulaciones.comentar');
        Route::get('postulaciones/{postulacion}/contacto', [PostulacionApiController::class, 'contacto'])->name('postulaciones.contacto');
        Route::get('postulaciones/{postulacion}/cv', [PostulacionApiController::class, 'cv'])->name('postulaciones.cv');
        Route::post('postulaciones/{postulacion}/entrevistas', [EntrevistaApiController::class, 'store'])->name('postulaciones.entrevistas.store');
        Route::post('entrevistas/{entrevista}/resultado', [EntrevistaApiController::class, 'resultado'])->name('entrevistas.resultado');
        Route::get('postulaciones/{postulacion}/oferta', [OfertaApiController::class, 'show'])->name('postulaciones.oferta.show');
        Route::post('postulaciones/{postulacion}/oferta', [OfertaApiController::class, 'store'])->name('postulaciones.oferta.store');
        Route::get('postulaciones/{postulacion}/archivos', [PostulacionApiController::class, 'listarArchivos'])->name('postulaciones.archivos.listar');
        Route::post('postulaciones/{postulacion}/archivos', [PostulacionApiController::class, 'subirArchivo'])->name('postulaciones.archivos.subir');
        Route::get('postulaciones/{postulacion}/archivos/{media}', [PostulacionApiController::class, 'descargarArchivo'])->name('postulaciones.archivos.descargar');
        Route::delete('postulaciones/{postulacion}/archivos/{media}', [PostulacionApiController::class, 'eliminarArchivo'])->name('postulaciones.archivos.eliminar');
        Route::apiResource('vacantes', VacanteApiController::class)->parameters(['vacantes' => 'vacante']);
        Route::apiResource('postulaciones', PostulacionApiController::class)->only(['index', 'store', 'show', 'update'])
            ->parameters(['postulaciones' => 'postulacion']);
    });
