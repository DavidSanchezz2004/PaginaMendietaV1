<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\App\AuthController;
use App\Http\Controllers\Api\App\AssignmentController;
use App\Http\Controllers\Api\App\JobController;
use App\Http\Controllers\Api\App\JobExecuteController;
use App\Http\Controllers\Api\App\ClientCompanyController;
use App\Http\Controllers\Api\App\ClientPortalAccountController;
use App\Http\Controllers\Api\App\ClientPortalCredentialController;
use App\Http\Controllers\Api\Robot\RobotViewerController;





Route::prefix('v1/app')->group(function () {

    // login sin auth (rate limit)
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'app_user', 'device_bound'])->group(function () {

        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/assignments', [AssignmentController::class, 'index']);

        Route::post('/jobs', [JobController::class, 'create'])->middleware('throttle:20,1');

        // secrets: token corto
        Route::post('/jobs/secrets', [JobController::class, 'secrets'])->middleware('throttle:30,1');

        // upload result
        Route::post('/jobs/result', [JobController::class, 'uploadResult'])->middleware('throttle:30,1');

        Route::post('/jobs/execute', [JobExecuteController::class, 'execute']);

        // ✅ Registro por cliente (app_users.type=cliente)
        Route::get('/companies', [ClientCompanyController::class, 'index']);
        Route::get('/companies/{company}', [ClientCompanyController::class, 'show']);
        Route::post('/companies', [ClientCompanyController::class, 'store']);
        Route::put('/companies/{company}', [ClientCompanyController::class, 'update']);
        Route::patch('/companies/{company}', [ClientCompanyController::class, 'update']);
        Route::delete('/companies/{company}', [ClientCompanyController::class, 'destroy']);
        Route::post('/portal-accounts', [ClientPortalAccountController::class, 'store']);
        Route::post('/portal-credentials', [ClientPortalCredentialController::class, 'store']);

    });
});

// Robot Viewer Proxy (requiere auth)
Route::prefix('v1/robot')->middleware(['auth:sanctum', 'app_user', 'device_bound'])->group(function () {
    Route::get('/viewer/{session_id}', [RobotViewerController::class, 'viewer']);
});

// Health check (sin auth)
Route::get('/v1/health', function () {
    return response()->json([
        'ok' => true,
        'message' => 'API is healthy',
        'timestamp' => now()->toIso8601String(),
    ]);
});
