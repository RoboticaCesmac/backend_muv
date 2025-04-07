<?php

namespace App\Http\Controllers\api\User;

use App\Services\api\User\UserWebService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserWebController extends Controller
{
    protected UserWebService $userWebService;
    
    public function __construct(UserWebService $userWebService)
    {
        $this->userWebService = $userWebService;
    }

    public function index(): JsonResponse
    {
        $users = $this->userWebService->index();

        return response()->json($users);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->userWebService->show($id));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->userWebService->destroy($id);

        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }
}