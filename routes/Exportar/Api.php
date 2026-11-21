<?php

use App\Http\Controllers\Api\ExportableDataTableApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('exportar')->group(function () {
    Route::post('/excel', [ExportableDataTableApiController::class, 'exportarExcel']);
    Route::post('/pdf', [ExportableDataTableApiController::class, 'exportarPdf']);
    Route::get('/publico/wordToPdf', [ExportableDataTableApiController::class, 'wordToPdf'])->withoutMiddleware('auth:sanctum');
});
