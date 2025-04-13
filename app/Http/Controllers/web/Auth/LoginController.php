<?php

namespace App\Http\Controllers\web\Auth;

use App\Services\JwtAuthService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;

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
                // Configura o tempo de expiração do token
                JWTAuth::factory()->setTTL(60 * 24); // 24 horas
                
                // Gera o token JWT
                $token = JWTAuth::fromUser(Auth::user());
                
                // Cria o cookie com o token
                $cookie = Cookie::make(
                    'auth_token',
                    $token,
                    60 * 24, // 24 horas
                    null,    // path
                    null,    // domain
                    true,    // secure
                    true,    // httpOnly
                    false,   // raw
                    'Lax'    // sameSite
                );

                // Redireciona para home com o cookie
                return redirect()
                    ->route('home')
                    ->withCookie($cookie);
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