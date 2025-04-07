<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    /**
     * Autentica um usuário e retorna o token JWT.
     *
     * @param User $user
     * @return string
     */
    protected function authenticateUser(User $user): string
    {
        return JWTAuth::fromUser($user);
    }

    /**
     * Faz uma requisição autenticada.
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param User $user
     * @return \Illuminate\Testing\TestResponse
     */
    protected function jsonAsUser(string $method, string $uri, array $data = [], User $user)
    {
        $token = $this->authenticateUser($user);

        return $this->json($method, $uri, $data, ['Authorization' => "Bearer $token"]);
    }
}