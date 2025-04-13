<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class HandleJwtAuth
{
    /**
     * Lista de rotas que não precisam de autenticação
     */
    protected $except = [
        '/',
        'login',
    ];

    public function handle(Request $request, Closure $next)
    {
        foreach ($this->except as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        if (! $request->bearerToken() && $request->hasCookie('auth_token')) {
            $token = $request->cookie('auth_token');
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            return $next($request);
        } catch (\Exception $e) {
            Cookie::queue(Cookie::forget('auth_token'));
            
            if ($request->header('X-Inertia')) {
                return redirect('/')->with('error', 'Sua sessão expirou. Por favor, faça login novamente.');
            }
            
            return redirect('/')->withErrors([
                'message' => 'Sua sessão expirou. Por favor, faça login novamente.'
            ]);
        }
    }
}
