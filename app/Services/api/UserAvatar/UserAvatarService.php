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

    public function update(Collection $data): UserAvatar
    {
        $user = auth()->user();
        
        $user->avatar_id = $data->get('avatar_id');

        $user->save();

        return $user;
    }
}