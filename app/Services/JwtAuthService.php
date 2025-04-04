<?php

namespace App\Services;

use App\Exceptions\Token\TokenNotFoundException;
use App\Exceptions\User\UserNotAdminException;
use App\Models\Token;
use App\Models\User;
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

        if (!$user->is_admin) {
            throw new UserNotAdminException('Você não tem permissão para acessar esta aplicação.');
        }

        return array_merge($this->respondWithToken($token));
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

        return array_merge($this->respondWithToken($token), ['first_login' => $user->first_login]);
    }

    /**
     * Register a new user and return a JWT token.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $token = Token::find($data['email']);

        if (!$token || $token->token !== encrypt($data['token'])) {
            throw new TokenNotFoundException('O token fornecido é inválido.');
        }

        if ($token->isExpired()) {
            throw new TokenNotFoundException('O token fornecido já está expirado.');
        }

        $user = User::create([
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'email_verified_at' => now(),
        ]); 

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token);
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