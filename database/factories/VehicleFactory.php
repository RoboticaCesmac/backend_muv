<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
            'co2_per_km' => fake()->randomFloat(2, 10, 100),
            'icon_path' => fake()->imageUrl(),
            'points_per_km' => fake()->randomFloat(1, 0, 10),
        ];
    }

    public function stateName(?string $name = null) {
        return $this->state([
            'name' => $name ?? fake()->name(),
        ]);
    }

    public function stateCo2PerKm(?float $co2PerKm = null) {
        return $this->state([
            'co2_per_km' => $co2PerKm ?? fake()->randomFloat(2, 10, 100),
        ]);
    }

    public function stateIconPath(?string $iconPath = null) {
        return $this->state([
            'icon_path' => $iconPath ?? fake()->imageUrl(),
        ]);
    }

    public function statePointsPerKm(?float $pointsPerKm = null) {
        return $this->state([
            'points_per_km' => $pointsPerKm ?? fake()->randomFloat(1, 0, 10),
        ]);
    }
}