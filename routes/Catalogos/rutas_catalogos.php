<?php
use App\Http\Controllers\Api\BandejaApiController;
use App\Http\Controllers\Api\Catalogos\GerenciaApiController;
    use App\Http\Controllers\Api\DepartamentoApiController;
    use App\Http\Controllers\Api\EntrevistaEstadoApiController;
    use App\Http\Controllers\Api\MunicipioApiController;
    use App\Http\Controllers\Api\PaisApiController;
    use App\Http\Controllers\Api\PermisoApiController;
use App\Http\Controllers\Api\Catalogos\PuestoApiController;
use App\Http\Controllers\Api\Catalogos\RegionApiController;
    use App\Http\Controllers\Api\PostulacionEstadoApiController;
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
    // RUTAS CATALOGO
    Route::apiResource('bandeja', BandejaApiController::class)->parameters(['bandeja' => 'bandeja']);
    Route::apiResource('departamentos', DepartamentoApiController::class)->parameters(['departamentos' => 'departamento']);
    Route::apiResource('entrevistas_estado', EntrevistaEstadoApiController::class)->parameters(['entrevistas_estado' => 'entrevistaestado']);
    Route::apiResource('gerencias', GerenciaApiController::class)->parameters(['gerencias' => 'gerencia']);
    Route::apiResource('municipios', MunicipioApiController::class)->parameters(['municipios' => 'municipio']);
    Route::apiResource('pais', PaisApiController::class)->parameters(['pais' => 'pais']);
    Route::apiResource('postulacion_estado', PostulacionEstadoApiController::class)->parameters(['postulacion_estado' => 'postulacionestado']);

});
