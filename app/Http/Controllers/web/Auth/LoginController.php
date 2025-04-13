<?php

namespace App\Http\Controllers\web\Auth;

use App\Services\JwtAuthService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function __construct(
        protected JwtAuthService $authService
    ) {}

    public function show()
    {
        return Inertia::render('Login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($request->only('email', 'password'))) {
                $token = JWTAuth::fromUser(Auth::user());
                
                $cookie = cookie('auth_token', $token, 60 * 24, null, null, true, true);
                
                return Inertia::location(route('home'))->withCookie($cookie);
            }
            
            return back()->withErrors([
                'error' => 'Credenciais inválidas'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Erro ao fazer login: ' . $e->getMessage()
            ]);
        }
    }
}