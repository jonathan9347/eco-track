<?php

use App\Http\Controllers\Api\CarbonCalculationController;
use App\Http\Controllers\Api\PredictionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/calculate-carbon', [CarbonCalculationController::class, 'calculate']);
    Route::post('/save-log', [CarbonCalculationController::class, 'saveLog']);
    Route::get('/user-logs', [CarbonCalculationController::class, 'getUserLogs']);
    Route::get('/leaderboard', [CarbonCalculationController::class, 'leaderboard']);
    Route::get('/predict', [PredictionController::class, 'predict']);
});
