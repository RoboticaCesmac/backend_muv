<?php

namespace App\Services;

use App\Exceptions\Token\TokenNotFoundException;
use App\Exceptions\User\UserNotAdminException;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthService
{

    public function login(array $credentials): array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $user = JWTAuth::user();

        if (!$user || !$user->is_admin) {
            throw new UserNotAdminException('Você não tem permissão para acessar esta aplicação.');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Authenticate a user and return a JWT token.
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function loginMobile(array $credentials): array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $user = JWTAuth::user();

        return array_merge($this->respondWithToken($token), ['is_first_login' => $user->is_first_login]);
    }

    /**
     * Register a new user and return a JWT token.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $token = Token::find($data['email']);

        if (!$token || $data['token'] !== Crypt::decryptString($token->token)) {
            throw new TokenNotFoundException('O token fornecido é inválido.');
        }

        if ($token->isExpired()) {
            throw new TokenNotFoundException('O token fornecido já está expirado.');
        }

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]); 

        return $user;
    }

    /**
     * Get the authenticated user.
     *
     * @return User
     */
    public function me(): User
    {
        return JWTAuth::parseToken()->authenticate();
    }

    /**
     * Logout the user (Invalidate the token).
     *
     * @return void
     */
    public function logout(): void
    {
        JWTAuth::parseToken()->invalidate();
    }

    /**
     * Refresh a token.
     *
     * @return array
     */
    public function refresh(): array
    {
        return $this->respondWithToken(JWTAuth::parseToken()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return array
     */
    protected function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }
} 