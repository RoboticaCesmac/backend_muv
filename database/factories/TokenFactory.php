<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

class TokenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => fake()->email(),
            'token' => Crypt::encryptString("123456"),
            'expires_at' => now()->addHours(1),
        ];
    }

    public function stateEmail(?string $email = null) {
        return $this->state([
            'email' => $email ?? fake()->email(),
        ]);
    }

    public function stateToken(?string $token = null) {
        return $this->state([
            'token' => Crypt::encryptString($token) ?? Crypt::encryptString(fake()->numberBetween(100000, 999999)),
        ]);
    }
}
