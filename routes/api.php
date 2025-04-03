<?php

use App\Http\Controllers\api\Auth\TokenController;
use App\Http\Controllers\api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('send-token', [TokenController::class, 'sendToken']);
        Route::get('verify-token', [TokenController::class, 'verifyToken']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });
});
