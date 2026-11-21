<?php
use App\Http\Controllers\Api\BandejaApiController;
use App\Http\Controllers\Api\Catalogos\GerenciaApiController;
use App\Http\Controllers\Api\PermisoApiController;
use App\Http\Controllers\Api\Catalogos\PuestoApiController;
use App\Http\Controllers\Api\Catalogos\RegionApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\Catalogos\UnidadApiController;

use Illuminate\Support\Facades\Route;

Route::prefix('catalogo')->name('catalogo.')->group(function () {
    Route::apiResources([
        'puestos' => PuestoApiController::class,
        'unidades' => UnidadApiController::class,
        'regiones' => RegionApiController::class,
        'gerencias' => GerenciaApiController::class,
        'roles' => RoleApiController::class,
        'permissions' => PermisoApiController::class,
    ]);

    Route::apiResource('bandeja', BandejaApiController::class)
        ->parameters(['bandeja' => 'bandeja']);

});
