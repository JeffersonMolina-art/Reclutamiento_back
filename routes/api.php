<?php

    use App\Http\Controllers\Api\BitacoraApiController;
    use App\Http\Controllers\Api\Publico\CandidatoAuthApiController;
use App\Http\Controllers\Api\Publico\CandidatoPublicoApiController;
use App\Http\Controllers\Api\Publico\VacantePublicaApiController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/Auth/api.php';
require __DIR__ . '/Candidato/api.php';
Route::middleware(['auth:sanctum', 'interno'])->name('api.')->group(function () {
    require __DIR__ . '/Users/rutas_users.php';
    require __DIR__ . '/Configuraciones/MenuOpcion/api.php';
    require __DIR__ . '/Catalogos/rutas_catalogos.php';
    require __DIR__ . '/Reclutamiento/api.php';
    require __DIR__ . '/Configuraciones/api.php';
    require __DIR__ . '/Desarrollo/rutas_desarrollo.php';
    require __DIR__ . '/Exportar/Api.php';

    Route::get('bitacoras', [BitacoraApiController::class, 'index']);

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



