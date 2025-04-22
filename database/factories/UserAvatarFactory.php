<?php

namespace Database\Factories;

use App\Models\UserAvatar;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAvatarFactory extends Factory
{
    protected $model = UserAvatar::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'avatar_path' => $this->faker->imageUrl(),
            'is_default' => $this->faker->boolean,
        ];
    }

    public function stateName(?string $name = null)
    {
        return $this->state([
            'name' => $name ?? $this->faker->name,
        ]);
    }

    public function stateAvatarPath(?string $avatarPath = null)
    {
        return $this->state([
            'avatar_path' => $avatarPath ?? $this->faker->imageUrl(),
        ]);
    }

    public function stateIsDefault(?bool $isDefault = null)
    {
        return $this->state([
            'is_default' => $isDefault ?? $this->faker->boolean,
        ]);
    }
}