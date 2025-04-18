<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Requests\api\Auth\ConfirmTokenRequest;
use App\Http\Requests\api\Auth\SendRegisterTokenRequest;
use App\Http\Requests\api\Auth\SendResetPasswordTokenRequest;
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

    public function sendRegisterToken(SendRegisterTokenRequest $request): JsonResponse
    {
        $this->tokenService->sendRegisterToken($request);

        return response()->json(['message' => 'Token enviado com sucesso']);
    }

    public function sendResetPasswordToken(SendResetPasswordTokenRequest $request): JsonResponse
    {
        $this->tokenService->sendResetPasswordToken($request);

        return response()->json(['message' => 'Token enviado com sucesso']);
    }

    /**
     * Confirma se um token é válido para um determinado email
     *
     * @param ConfirmTokenRequest $request
     * @return JsonResponse
     */
    public function confirmResetPasswordToken(ConfirmTokenRequest $request): JsonResponse
    {
        $this->tokenService->confirmResetPasswordToken($request->validated());

        return response()->json(['message' => 'Token válido']);
    }
}
