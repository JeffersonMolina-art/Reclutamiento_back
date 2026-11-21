<?php

use App\Http\Controllers\Api\Configuraciones\ConfiguracionApiController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Configuraciones\MenuOpcionApiController;

Route::apiResource('menu_opciones', MenuOpcionApiController::class);

Route::post('menu_opciones/actualizar/orden', [MenuOpcionApiController::class, 'actualizarOrden'])->name('menu_opciones.actualizar.orden');
