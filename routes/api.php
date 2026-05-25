<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BloodBagController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\BloodBankController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Role Access:
| - admin: Full access (CRUD + analytics + user management)
| - blood_bank_staff: Can manage blood bags (create, update, delete) + view analytics
| - monitoring_user: Read-only access (view blood bags + dashboard)
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {

    // Auth routes (all roles)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Read-only routes (all authenticated roles can view)
    Route::get('/blood-bags', [BloodBagController::class, 'index']);
    Route::get('/blood-bags/{blood_bag}', [BloodBagController::class, 'show']);
    Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
    Route::get('/refrigerators/{refrigerator}/analysis', [AnalyticsController::class, 'refrigeratorAnalysis']);
    Route::get('/blood-banks', [BloodBankController::class, 'index']);
    Route::get('/blood-banks/{blood_bank}', [BloodBankController::class, 'show']);
    Route::get('/refrigerators/{refrigerator}/temperature-logs', [\App\Http\Controllers\Api\TemperatureLogController::class, 'indexForRefrigerator']);
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);

    // Blood Bag Management (admin & staff only — can create, update, delete)
    Route::middleware('role:admin,blood_bank_staff')->group(function () {
        Route::post('/blood-bags', [BloodBagController::class, 'store']);
        Route::put('/blood-bags/{blood_bag}', [BloodBagController::class, 'update']);
        Route::patch('/blood-bags/{blood_bag}', [BloodBagController::class, 'update']);
        Route::delete('/blood-bags/{blood_bag}', [BloodBagController::class, 'destroy']);
        Route::post('/temperature-logs', [\App\Http\Controllers\Api\TemperatureLogController::class, 'store']);
    });

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/blood-banks', [BloodBankController::class, 'store']);
        Route::put('/blood-banks/{blood_bank}', [BloodBankController::class, 'update']);
        Route::patch('/blood-banks/{blood_bank}', [BloodBankController::class, 'update']);
        Route::delete('/blood-banks/{blood_bank}', [BloodBankController::class, 'destroy']);
    });
});
