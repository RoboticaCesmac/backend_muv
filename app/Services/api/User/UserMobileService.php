<?php

namespace App\Services\api\User;

use App\Exceptions\User\UserHasAlreadyLoggedInFirst;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserMobileService
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function firstLogin(array $data, int $id): User
    {
        $loggedUser = auth()->user();

        if (!$loggedUser->isFirstLogin()) {
            throw new UserHasAlreadyLoggedInFirst('Usuário já realizou o primeiro login');
        }

        $loggedUser->update([
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'user_name' => $data['user_name'],
            'vehicle_id' => $data['vehicle_id'],
            'is_first_login' => false,
        ]);

        return $loggedUser;
    }
}