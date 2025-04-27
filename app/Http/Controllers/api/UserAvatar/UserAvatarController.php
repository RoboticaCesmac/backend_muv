<?php

namespace App\Http\Controllers\api\UserAvatar;

use Illuminate\Http\JsonResponse;
use App\Services\api\UserAvatar\UserAvatarService;
use Illuminate\Routing\Controller;

class UserAvatarController extends Controller
{
    public function __construct(private UserAvatarService $userAvatarService)
    {
        $this->userAvatarService = $userAvatarService;
    }

    public function all(): JsonResponse
    {
        $userAvatars = $this->userAvatarService->all();

        return response()->json(['data' => $userAvatars]);
    }
}
