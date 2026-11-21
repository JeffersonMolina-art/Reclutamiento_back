<?php

use App\Http\Controllers\Api\Configuraciones\UserPreferenciaController;
use Illuminate\Support\Facades\Route;


Route::apiResource('user_preferencias', UserPreferenciaController::class);

