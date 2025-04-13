<?php

use App\Http\Controllers\web\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Login');
});

Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::middleware(['jwt.auth'])->group(function () {

    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::get('/dashboard', function () {
        return Inertia::render('Home', [
            'title' => 'Dashboard',
            'description' => 'Bem-vindo ao seu painel de controle'
        ]);
    })->name('dashboard');

    Route::get('/projects', function () {
        return Inertia::render('Projects', [
            'title' => 'Projetos'
        ]);
    })->name('projects');

    Route::get('/reports', function () {
        return Inertia::render('Reports', [
            'title' => 'Relatórios'
        ]);
    })->name('reports');

    Route::get('/settings', function () {
        return Inertia::render('Settings', [
            'title' => 'Configurações'
        ]);
    })->name('settings');
});
