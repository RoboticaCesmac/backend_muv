<?php

namespace App\Http\Controllers\api\User;

use App\Http\Requests\api\Auth\FirstLoginRequest;
use App\Services\api\User\UserMobileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserMobileController extends Controller
{
    public function __construct(private UserMobileService $userMobileService)
    {
        $this->userMobileService = $userMobileService;
    }

    public function firstLogin(FirstLoginRequest $request, int $id): JsonResponse
    {
        $this->userMobileService->firstLogin($request->all(), $id);

        return response()->json(['message' => 'Primeiro login realizado com sucesso']);
    }
}