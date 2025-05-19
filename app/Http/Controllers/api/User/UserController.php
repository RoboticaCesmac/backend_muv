<?php

namespace App\Http\Controllers\api\User;

use App\Http\Requests\api\Auth\FirstLoginRequest;
use App\Http\Requests\api\Auth\ResetPasswordRequest;
use App\Http\Requests\api\User\UserAvatarUpdateRequest;
use App\Http\Requests\api\User\UserVehicleUpdateRequest;
use App\Services\api\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
        $this->userService = $userService;
    }

    public function firstLogin(FirstLoginRequest $request): JsonResponse
    {
        $user = $this->userService->firstLogin($request->validated());

        return response()->json(['message' => 'Primeiro login realizado com sucesso', 'data' => $user]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->userService->resetPassword($request->validated());

        return response()->json(['message' => 'Senha alterada com sucesso']);
    }

    public function updateAvatar(UserAvatarUpdateRequest $request): JsonResponse
    {
        $this->userService->updateAvatar(collect($request->validated()));

        return response()->json([
            'sucess' => true,
        ]);
    }

    public function updateVehicle(UserVehicleUpdateRequest $request): JsonResponse
    {
        $this->userService->updateVehicle(collect($request->validated()));

        return response()->json([
            'sucess' => true,
        ]);
    }

    public function destroy(): JsonResponse
    {
        $this->userService->destroy();

        return response()->json([
            'sucess' => true,
        ]);
    }
}