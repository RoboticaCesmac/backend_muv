<?php

namespace App\Http\Controllers\api\User;

use App\Http\Requests\api\Auth\FirstLoginRequest;
use App\Services\api\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    protected UserService $userService;
    
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->userService->index());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->userService->show($id));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->userService->destroy($id);

        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }

    public function firstLogin(FirstLoginRequest $request, int $id): JsonResponse
    {
        $this->userService->firstLogin($request->all(), $id);

        return response()->json(['message' => 'Primeiro login realizado com sucesso']);
    }
}