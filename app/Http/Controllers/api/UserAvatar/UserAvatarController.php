<?php

namespace App\Http\Controllers\api\UserAvatar;

use App\Http\Requests\api\Avatar\UserAvatarUpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Services\api\UserAvatar\UserAvatarService;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserAvatar\UserAvatarUrlResource;

class UserAvatarController extends Controller
{
    public function __construct(private UserAvatarService $userAvatarService)
    {
        $this->userAvatarService = $userAvatarService;
    }

    public function all(): JsonResponse
    {
        $userAvatars = $this->userAvatarService->all();

        return response()->json([
            'data' => UserAvatarUrlResource::collection($userAvatars)
        ]);
    }

    public function update(UserAvatarUpdateRequest $request): JsonResponse
    {
        $userAvatar = $this->userAvatarService->update(collect($request->validated()));

        return response()->json([
            'data' => new UserAvatarUrlResource($userAvatar)
        ]);
    }
}
