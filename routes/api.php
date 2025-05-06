<?php

use App\Http\Controllers\api\Auth\TokenController;
use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\Route\RouteMobileController;
use App\Http\Controllers\api\User\UserMobileController;
use App\Http\Controllers\api\Vehicle\VehicleMobileController;
use App\Http\Controllers\api\UserAvatar\UserAvatarController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('login-mobile', [AuthController::class, 'loginMobile']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('send-register-token', [TokenController::class, 'sendRegisterToken']);
        Route::post('send-reset-password-token', [TokenController::class, 'sendResetPasswordToken']);
        Route::post('confirm-reset-password-token', [TokenController::class, 'confirmResetPasswordToken']);
        Route::post('reset-password', [UserMobileController::class, 'resetPassword']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::prefix('mobile')->group(function () {
            Route::post('user/first-login', [UserMobileController::class, 'firstLogin']);
            Route::get('vehicle', [VehicleMobileController::class, 'all']);
            Route::get('user-avatar', [UserAvatarController::class, 'all']);
            Route::prefix('route')->group(function () {
                Route::get('', [RouteMobileController::class, 'index']);
                Route::get('route-screen', [RouteMobileController::class, 'routeScreen']);
                Route::post('start', [RouteMobileController::class, 'start']);
                Route::post('finish', [RouteMobileController::class, 'finish']);
                Route::post('points', [RouteMobileController::class, 'points']);
            });
        });
    });
});
