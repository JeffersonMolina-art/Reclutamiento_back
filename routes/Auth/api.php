<?php


use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\Auth\AuthAzureApiController;
use App\Http\Controllers\Api\Auth\AuthDobleFactorApiController;
use App\Http\Controllers\Api\Configuraciones\ImagenConfiguracionApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('login', [AuthApiController::class, 'login'])->name('login');
    Route::post('logout', [AuthApiController::class, 'logout'])
        ->middleware('auth:sanctum')->name('logout');
    Route::prefix('doble-factor')->name('doble.factor.')->group(function () {

        Route::get('generar-qr/{user}', [AuthDobleFactorApiController::class, 'crearQr']);

        Route::post('confirmar-codigo', [AuthDobleFactorApiController::class, 'confirmarDobleFactor']);

        Route::post('validar-codigo-auth', [AuthDobleFactorApiController::class, 'validarCodigoAuth']);

        Route::put('reestablecer-codigo/{user}', [AuthDobleFactorApiController::class, 'reiniciarDobleFactor']);

    });


    Route::get('fondo/login', [ImagenConfiguracionApiController::class, 'ObtenerImagenFondo']);

    Route::post('/microsoft/login', [AuthAzureApiController::class, 'login'])->name('microsoft.login');
});

Route::get('/user', function (Request $request) {
    return $request->user()->userResponse();
})->middleware('auth:sanctum')->name('api.user');
