<?php

use App\Http\Controllers\web\Auth\LoginController;
use App\Http\Controllers\web\UserController;
use App\Http\Controllers\web\UserLevelController;
use App\Http\Controllers\web\VehicleController;
use App\Http\Controllers\web\AvatarController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Login');
});

Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::middleware(['jwt.auth'])->group(function () {

    Route::get('/home', [UserController::class, 'index'])->name('home');
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/export-excel', [UserController::class, 'exportExcel'])->name('users.export-excel');

    Route::get('/dashboard', function () {
        return Inertia::render('Home', [
            'title' => 'Dashboard',
            'description' => 'Bem-vindo ao seu painel de controle'
        ]);
    })->name('dashboard');

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');

    Route::get('/levels', [UserLevelController::class, 'index'])->name('levels');
    Route::put('/levels/{level}', [UserLevelController::class, 'update'])->name('levels.update');

    Route::get('/avatars', [AvatarController::class, 'index'])->name('avatars');
    Route::post('/avatars', [AvatarController::class, 'store'])->name('avatars.store');
    Route::delete('/avatars/{avatar}', [AvatarController::class, 'destroy'])->name('avatars.destroy');
});
