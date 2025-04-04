<?php

namespace App\Services\api\User;

use App\Exceptions\User\UserHasAlreadyLoggedInFirst;
use App\Exceptions\User\UserNotAdminException;
use App\Exceptions\User\UserNotFoundException;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index(): LengthAwarePaginator
    {
        return $this->user->paginate(10);
    }

    public function show(int $id): User
    {
        $user = $this->user->find($id);
        
        if (!$user) {
            throw new UserNotFoundException('Usuário não encontrado');
        }
        return $user;
    }

    public function destroy(int $id): User
    {
        $loggedUser = JWTAuth::user();
        
        $userDestroy = $this->user->find($id);

        if (!$userDestroy) {
            throw new UserNotFoundException('Usuário não encontrado');
        }

        if (!$loggedUser->is_admin) {
            throw new UserNotAdminException('Usuário não é administrador');
        }

        $userDestroy->delete();

        return $userDestroy;
    }

    public function firstLogin(array $data, int $id): User
    {
        $user = JWTAuth::user();

        if (!$user->first_login) {
            throw new UserHasAlreadyLoggedInFirst('Usuário já realizou o primeiro login');
        }

        $user->update([
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'user_name' => $data['user_name'],
            'vehicle_id' => $data['vehicle_id'],
            'first_login' => true,
        ]);

        return $user;
    }
}   