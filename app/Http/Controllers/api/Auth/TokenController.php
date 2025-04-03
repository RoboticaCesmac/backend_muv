<?php

namespace App\Http\Controllers\api\Auth;

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
}
