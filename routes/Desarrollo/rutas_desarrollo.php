<?php


use App\Http\Controllers\Api\PruebaApiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'desarrollo', 'as' => 'desarrollo.'], function () {
    Route::post('/notificaciones', [PruebaApiController::class, 'enviarNotificacion'])->name('notificaciones');
    Route::post('/mensajes', [PruebaApiController::class, 'enviarSMSTelefono'])->name('mensajes');
});
