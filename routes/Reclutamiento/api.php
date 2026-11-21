<?php

    use App\Http\Controllers\Api\VacanteApiController;
    use Illuminate\Support\Facades\Route;

    Route::prefix('reclutamiento')->name('reclutamiento.')->group(function () {
        Route::apiResource('vacantes', VacanteApiController::class)->parameters(['vacantes' => 'vacante']);
    });
