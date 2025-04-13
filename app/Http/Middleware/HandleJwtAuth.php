<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class HandleJwtAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->bearerToken() && $request->hasCookie('auth_token')) {
            $token = $request->cookie('auth_token');
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        try {
            JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido ou não fornecido'], 401);
        }

        return $next($request);
    }
}
