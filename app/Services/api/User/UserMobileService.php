<?php

namespace App\Services\api\User;

use App\Exceptions\Token\TokenNotFoundException;
use App\Exceptions\User\UserHasAlreadyLoggedInFirst;
use App\Exceptions\User\UserNotFoundException;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

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
        $user = $this->user->where('email', $data['email'])->first();
        
        if (!$user) {
            throw new UserNotFoundException('Usuário não encontrado');
        }

        $token = Token::find($data['email']);

        if (!$token || $data['token'] !== Crypt::decryptString($token->token)) {
            throw new TokenNotFoundException('O token fornecido é inválido.');
        }

        if ($token->isExpired()) {
            throw new TokenNotFoundException('O token fornecido já está expirado.');
        }

        $user->update([
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);

        return $user;
    }
}
