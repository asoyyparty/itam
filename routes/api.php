<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAssetController;
use App\Http\Controllers\Api\ApiDashboardController;
use App\Http\Controllers\Api\ApiTicketController;
use App\Http\Controllers\Api\ApiPredictiveHealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for ITAM Mobile App (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public Authentication Endpoint
    Route::post('/login', [ApiAuthController::class, 'login']);

    // Agent sync endpoint
    Route::post('/ips/agent-sync', [\App\Http\Controllers\IpAddressController::class, 'agentSync']);

    // Authenticated API Routes
    Route::middleware([\App\Http\Middleware\ApiTokenMiddleware::class])->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/me', [ApiAuthController::class, 'me']);

        // Dashboard Telemetry
        Route::get('/dashboard', [ApiDashboardController::class, 'index']);

        // Asset Management & Mobile QR Scanner
        Route::get('/assets', [ApiAssetController::class, 'index']);
        Route::get('/assets/{id}', [ApiAssetController::class, 'show'])->where('id', '[0-9]+');
        Route::get('/assets/scan/{tag}', [ApiAssetController::class, 'scanQr']);

        // Tickets Support
        Route::get('/tickets', [ApiTicketController::class, 'index']);
        Route::post('/tickets', [ApiTicketController::class, 'store']);
        Route::get('/tickets/{id}', [ApiTicketController::class, 'show']);

        // Predictive Health AI Diagnosis
        Route::get('/predictive-health', [ApiPredictiveHealthController::class, 'index']);
        Route::get('/predictive-health/{asset}/analyze', [ApiPredictiveHealthController::class, 'analyze']);
    });
});
