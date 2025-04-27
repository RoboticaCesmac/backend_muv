<?php

namespace App\Services\api\UserAvatar;
use App\Models\UserAvatar;
use Illuminate\Database\Eloquent\Collection;

class UserAvatarService
{
    public function all(): Collection
    {
        return UserAvatar::all();
    }
}