<?php

use App\Http\Controllers\api\Auth\TokenController;
use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\User\UserMobileController;
use App\Http\Controllers\api\User\UserWebController;
use App\Http\Controllers\api\Vehicle\VehicleMobileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('login-mobile', [AuthController::class, 'loginMobile']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('send-token', [TokenController::class, 'sendToken']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::prefix('web')->group(function () {
            Route::apiResource('user', UserWebController::class, ['only' => ['index', 'show', 'destroy']]);
        });

        Route::prefix('mobile')->group(function () {
            Route::post('user/{id}/first-login', [UserMobileController::class, 'firstLogin']);
            Route::get('vehicle', [VehicleMobileController::class, 'all']);
        });
    });
});
