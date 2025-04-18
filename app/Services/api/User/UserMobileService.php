<?php

namespace App\Services\api\User;

use App\Exceptions\User\UserHasAlreadyLoggedInFirst;
use App\Exceptions\User\UserNotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserMobileService
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function firstLogin(array $data): User
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

    public function resetPassword(array $data): User
    {
        $user = User::where('email', $data['email'])->first();
        
        if (!$user) {
            throw new UserNotFoundException('Usuário não encontrado');
        }
        
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }
}
