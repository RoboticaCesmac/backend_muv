<?php

namespace App\Http\Controllers\api\User;

use App\Http\Requests\api\Auth\FirstLoginRequest;
use App\Http\Requests\api\Auth\ResetPasswordRequest;
use App\Services\api\User\UserMobileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserMobileController extends Controller
{
    public function __construct(private UserMobileService $userMobileService)
    {
        $this->userMobileService = $userMobileService;
    }

    public function firstLogin(FirstLoginRequest $request): JsonResponse
    {
        $user = $this->userMobileService->firstLogin($request->validated());

        return response()->json(['message' => 'Primeiro login realizado com sucesso', 'data' => $user]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->userMobileService->resetPassword($request->validated());

        return response()->json(['message' => 'Senha alterada com sucesso']);
    }
}