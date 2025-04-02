<?php

use App\Exceptions\User\UserNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return response()->json(throw new UserNotFoundException('Usuário não encontrado'));
});
