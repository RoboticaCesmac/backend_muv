<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Requests\api\Auth\ConfirmTokenRequest;
use App\Http\Requests\api\Auth\SendTokenRequest;
use App\Services\api\Auth\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class TokenController extends Controller
{
    protected TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function sendToken(SendTokenRequest $request): JsonResponse
    {
        $this->tokenService->sendToken($request);

        return response()->json(['message' => 'Token enviado com sucesso']);
    }

    /**
     * Confirma se um token é válido para um determinado email
     *
     * @param ConfirmTokenRequest $request
     * @return JsonResponse
     */
    public function confirmToken(ConfirmTokenRequest $request): JsonResponse
    {
        $this->tokenService->confirmToken($request->validated());

        return response()->json(['message' => 'Token válido']);
    }
}
