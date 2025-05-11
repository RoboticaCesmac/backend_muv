<?php

use App\Http\Controllers\api\Auth\TokenController;
use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\Route\RouteController;
use App\Http\Controllers\api\User\UserController;
use App\Http\Controllers\api\Vehicle\VehicleController;
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
        Route::post('reset-password', [UserController::class, 'resetPassword']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::prefix('mobile')->group(function () {
            Route::prefix('vehicle')->group(function () {
                Route::get('/all', [VehicleController::class, 'all']);
            });

            Route::prefix('avatar')->group(function () {
                Route::get('/all', [UserAvatarController::class, 'all']);
            });

            Route::prefix('user')->group(function () {
                Route::post('first-login', [UserController::class, 'firstLogin']);
                Route::patch('user-vehicle', [UserController::class, 'updateVehicle']);
                Route::patch('user-avatar', [UserController::class, 'updateAvatar']);
            });

            Route::prefix('route')->group(function () {
                Route::get('', [RouteController::class, 'getPaginated']);
                Route::get('route-screen', [RouteController::class, 'routeScreen']);
                Route::post('start', [RouteController::class, 'start']);
                Route::post('finish', [RouteController::class, 'finish']);
                Route::post('points', [RouteController::class, 'points']);
            });
        });
    });
});
