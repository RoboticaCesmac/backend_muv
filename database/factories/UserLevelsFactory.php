<?php

namespace Database\Factories;

use App\Models\UserLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserLevelsFactory extends Factory
{
    protected $model = UserLevel::class;

    public function definition()
    {
        return [
            'level_number' => fake()->numberBetween(1, 10),
            'points_required' => fake()->numberBetween(100, 1000),
            'icon_path' => fake()->imageUrl(),
            'is_default' => false,
        ];
    }

    public function stateLevelNumber(?int $levelNumber = null) {
        return $this->state([
            'level_number' => $levelNumber ?? fake()->numberBetween(1, 10),
        ]);
    }

    public function statePointsRequired(?int $pointsRequired = null) {
        return $this->state([
            'points_required' => $pointsRequired ?? fake()->numberBetween(100, 1000),
        ]);
    }

    public function stateIconPath(?string $iconPath = null) {
        return $this->state([
            'icon_path' => $iconPath ?? fake()->imageUrl(),
        ]);
    }

    public function stateIsDefault(?bool $isDefault = null) {
        return $this->state([
            'is_default' => $isDefault ?? false,
        ]);
    }
}