<?php

    use App\Http\Controllers\Api\Publico\CandidatoAuthApiController;
    use App\Http\Controllers\Api\Publico\PerfilCandidatoApiController;
    use App\Http\Controllers\Api\Publico\PerfilItemsCandidatoApiController;
    use App\Http\Controllers\Api\Publico\PostulacionCandidatoApiController;
    use Illuminate\Support\Facades\Route;

    Route::prefix('portal')->name('portal.')->middleware(['auth:sanctum', 'candidato'])->group(function () {
        // APIS DE AUTETICACION CANDIDATOS
        Route::get('me', [CandidatoAuthApiController::class, 'me'])->name('me');
        Route::post('logout', [CandidatoAuthApiController::class, 'logout'])->name('logout');
        // POSTULACIONES CANDIDATO
        Route::get('postulaciones', [PostulacionCandidatoApiController::class, 'index'])->name('postulaciones.index');
        Route::post('postulaciones', [PostulacionCandidatoApiController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('postulaciones.store');
        // TRACKING DE POSTULACIONES CANDIDATO
        Route::get('postulaciones/{postulacion}/tracking', [PostulacionCandidatoApiController::class, 'tracking'])
            ->whereNumber('postulacion')
            ->name('postulaciones.tracking');
        // PERFIL CANDIDATO
        Route::get('perfil', [PerfilCandidatoApiController::class, 'show'])->name('perfil.show');
        Route::put('perfil', [PerfilCandidatoApiController::class, 'update'])->name('perfil.update');
        // CURRICULUM VITAE CANDIDATO
        Route::get('perfil/cv', [PerfilCandidatoApiController::class, 'cv'])->name('perfil.cv');
        Route::get('perfil/cv/descargar', [PerfilCandidatoApiController::class, 'descargarCv'])->name('perfil.cv.descargar');
        Route::post('perfil/cv', [PerfilCandidatoApiController::class, 'subirCv'])
            ->middleware('throttle:10,1')
            ->name('perfil.cv.subir');
        Route::delete('perfil/cv', [PerfilCandidatoApiController::class, 'eliminarCv'])->name('perfil.cv.eliminar');
        // PERFIL CANDIDATO
        Route::prefix('perfil/{recurso}')
            ->whereIn('recurso', ['conocimientos', 'experiencias', 'formaciones'])
            ->whereNumber('id')
            ->group(function () {
                Route::get('/', [PerfilItemsCandidatoApiController::class, 'index'])->name('perfil.items.index');
                Route::post('/', [PerfilItemsCandidatoApiController::class, 'store'])->middleware('throttle:30,1')->name('perfil.items.store');
                Route::put('{id}', [PerfilItemsCandidatoApiController::class, 'update'])->name('perfil.items.update');
                Route::delete('{id}', [PerfilItemsCandidatoApiController::class, 'destroy'])->name('perfil.items.destroy');
            });
    });




