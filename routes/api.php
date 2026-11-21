<?php

use App\Http\Controllers\Api\Configuraciones\ConfiguracionApiController;
use App\Http\Controllers\Api\Publico\CandidatoAuthApiController;
use App\Http\Controllers\Api\Publico\CandidatoPublicoApiController;
use App\Http\Controllers\Api\Publico\PerfilCandidatoApiController;
use App\Http\Controllers\Api\Publico\PerfilItemsCandidatoApiController;
use App\Http\Controllers\Api\Publico\PostulacionCandidatoApiController;
use App\Http\Controllers\Api\Publico\VacantePublicaApiController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/Auth/api.php';

Route::middleware(['auth:sanctum', 'interno'])->name('api.')->group(function () {



    require __DIR__ . '/Users/rutas_users.php';
    require __DIR__ . '/Configuraciones/MenuOpcion/api.php';
    require __DIR__ . '/Catalogos/rutas_catalogos.php';
    require __DIR__ . '/Configuraciones/api.php';
    require __DIR__ . '/Desarrollo/rutas_desarrollo.php';
    require __DIR__ . '/Exportar/Api.php';

});

// APIS EXPUESTAS SIN AUTENTICACION
Route::prefix('publico')->name('publico.')->middleware('throttle:publico')->group(function () {
    Route::get('vacantes', [VacantePublicaApiController::class, 'index'])->name('vacantes.index');

    Route::post('candidatos/registro', [CandidatoPublicoApiController::class, 'registro'])
        ->middleware('throttle:registro-candidato')
        ->name('candidatos.registro');

    Route::post('candidatos/login', [CandidatoAuthApiController::class, 'login'])
        ->middleware('throttle:login-candidato')
        ->name('candidatos.login');
});

// PORTAL DEL CANDIDATO
Route::prefix('portal')->name('portal.')->middleware(['auth:sanctum', 'candidato'])->group(function () {
    Route::get('me', [CandidatoAuthApiController::class, 'me'])->name('me');
    Route::post('logout', [CandidatoAuthApiController::class, 'logout'])->name('logout');
    Route::get('postulaciones', [PostulacionCandidatoApiController::class, 'index'])->name('postulaciones.index');
    Route::post('postulaciones', [PostulacionCandidatoApiController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('postulaciones.store');
    Route::get('postulaciones/{postulacion}/tracking', [PostulacionCandidatoApiController::class, 'tracking'])
        ->whereNumber('postulacion')
        ->name('postulaciones.tracking');

    Route::get('perfil', [PerfilCandidatoApiController::class, 'show'])->name('perfil.show');
    Route::put('perfil', [PerfilCandidatoApiController::class, 'update'])->name('perfil.update');

    Route::get('perfil/cv', [PerfilCandidatoApiController::class, 'cv'])->name('perfil.cv');
    Route::get('perfil/cv/descargar', [PerfilCandidatoApiController::class, 'descargarCv'])->name('perfil.cv.descargar');
    Route::post('perfil/cv', [PerfilCandidatoApiController::class, 'subirCv'])
        ->middleware('throttle:10,1')
        ->name('perfil.cv.subir');
    Route::delete('perfil/cv', [PerfilCandidatoApiController::class, 'eliminarCv'])->name('perfil.cv.eliminar');

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




