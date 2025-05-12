<?php

namespace App\Http\Requests\api\User;

use Illuminate\Foundation\Http\FormRequest;

class UserAvatarUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar_id' => ['required', 'integer', 'exists:user_avatars,id'],
        ];
    }
}