<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware(['auth:api', 'organization'])->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('customers', [CustomerController::class, 'index']);
        Route::get('customers/{customerId}', [CustomerController::class, 'show']);
        Route::post('customers', [CustomerController::class, 'store']);
        Route::put('customers/{customerId}', [CustomerController::class, 'update']);
        Route::delete('customers/{customerId}', [CustomerController::class, 'destroy']);
    });
});
