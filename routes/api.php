<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DeliveryServiceController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ShipmentController;
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

        Route::get('/customers/{customer}/addresses', [AddressController::class, 'index']);
        Route::get('/customers/{customer}/addresses/{address}', [AddressController::class, 'show']);
        Route::post('/customers/{customer}/addresses', [AddressController::class, 'store']);
        Route::put('/customers/{customer}/addresses/{address}',[AddressController::class, 'update']);
        Route::delete('/customers/{customer}/addresses/{address}', [AddressController::class, 'destroy']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

        Route::get('/delivery-services', [DeliveryServiceController::class, 'index']);
        Route::post('/delivery-services', [DeliveryServiceController::class, 'store']);

        Route::get('/shipments', [ShipmentController::class, 'index']);
        Route::get('/shipments/{shipment}', [ShipmentController::class, 'show']);
        Route::post('/shipments', [ShipmentController::class, 'store']);
        Route::patch('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);
    });
});
