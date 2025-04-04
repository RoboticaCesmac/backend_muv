<?php

use App\Http\Controllers\api\Auth\TokenController;
use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\User\UserController;
use App\Http\Controllers\api\Vehicle\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('send-token', [TokenController::class, 'sendToken']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::apiResource('user', UserController::class, ['only' => ['index', 'show', 'destroy']]);
        Route::post('user/{id}/first-login', [UserController::class, 'firstLogin']);

        Route::get('vehicle', [VehicleController::class, 'index']);
    });
});
