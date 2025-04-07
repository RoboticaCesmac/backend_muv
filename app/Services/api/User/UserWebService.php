<?php

namespace App\Services\api\User;

use App\Exceptions\User\UserNotAdminException;
use App\Exceptions\User\UserNotFoundException;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserWebService
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index(): LengthAwarePaginator
    {
        return $this->user->where('email', '!=', 'ADMIN')->paginate(10);
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
        $loggedUser = auth()->user();
        
        $userDestroy = $this->user->where('id', $id)
            ->where('email', '!=', 'ADMIN')
            ->first();

        if (!$loggedUser->is_admin) {
            throw new UserNotAdminException('Usuário não é administrador');
        }

        if (!$userDestroy) {
            throw new UserNotFoundException('Usuário não encontrado');
        }

        $userDestroy->delete();

        return $userDestroy;
    }
}   