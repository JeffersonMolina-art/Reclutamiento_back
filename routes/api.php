<?php

use App\Http\Controllers\Api\Configuraciones\ConfiguracionApiController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/Auth/api.php';

Route::middleware('auth:sanctum')->name('api.')->group(function () {



    require __DIR__ . '/Users/rutas_users.php';
    require __DIR__ . '/Configuraciones/MenuOpcion/api.php';
    require __DIR__ . '/Catalogos/rutas_catalogos.php';
    require __DIR__ . '/Configuraciones/api.php';
    require __DIR__ . '/Desarrollo/rutas_desarrollo.php';
    require __DIR__ . '/Exportar/Api.php';

});

// APIS EXPUESTAS SIN AUTENTICACION
Route::get('configuracion', [ConfiguracionApiController::class, 'index'])->name('configuracion.index');




