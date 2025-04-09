<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Requests\api\Auth\LoginRequest;
use App\Http\Requests\api\Auth\RegisterRequest;
use App\Services\JwtAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct(
        protected JwtAuthService $authService
    ) {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'loginMobile']]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        
        return response()->json($this->authService->login($credentials), 200);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function loginMobile(LoginRequest $request): JsonResponse
    {
        return response()->json($this->authService->loginMobile($request->validated()), 200);
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authService->register($request->validated());

        return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return response()->json($this->authService->me());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return response()->json($this->authService->refresh());
    }
} 