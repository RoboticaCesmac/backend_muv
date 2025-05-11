<?php

namespace App\Services\api\UserAvatar;
use App\Models\UserAvatar;
use App\Models\User;
use Illuminate\Support\Collection;

class UserAvatarService
{
    public function all(): Collection
    {
        return UserAvatar::all();
    }

    public function update(Collection $data): User
    {
        $user = auth()->user();
        
        $user->avatar_id = $data->get('avatar_id');

        $user->save();

        return $user;
    }
}