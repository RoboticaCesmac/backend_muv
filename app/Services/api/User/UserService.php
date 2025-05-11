<?php

namespace App\Services\api\User;
use App\Exceptions\User\UserHasAlreadyLoggedInFirst;
use App\Exceptions\User\UserNotFoundException;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function firstLogin(array $data): User
    {
        $loggedUser = auth()->user();

        if (! $loggedUser || ! $loggedUser->isFirstLogin()) {
            throw new UserHasAlreadyLoggedInFirst('Usuário já realizou o primeiro login');
        }

        $loggedUser->update([
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'user_name' => $data['user_name'],
            'vehicle_id' => $data['vehicle_id'],
            'is_first_login' => false,
            'avatar_id' => $data['avatar_id'],
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

    public function updateAvatar(Collection $data): User
    {
        $user = auth()->user();
        
        $user->avatar_id = $data->get('avatar_id');

        $user->save();

        return $user;
    }

    public function updateVehicle(Collection $data): User
    {
        $user = auth()->user();
        
        $user->vehicle_id = $data->get('vehicle_id');

        $user->save();

        return $user;
    }
}
