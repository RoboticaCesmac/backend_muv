<?php

namespace App\Services\api\UserAvatar;
use App\Models\UserAvatar;
use Illuminate\Support\Collection;

class UserAvatarService
{
    public function all(): Collection
    {
        return UserAvatar::all();
    }
}