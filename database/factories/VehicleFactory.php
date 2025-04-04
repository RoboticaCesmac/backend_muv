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
        ];
    }

    public function stateName(?string $name = null) {
        return $this->state(fn (array $attributes) => [
            'name' => $name ?? fake()->name(),
        ]);
    }

    public function stateCo2PerKm(?float $co2PerKm = null) {
        return $this->state(fn (array $attributes) => [
            'co2_per_km' => $co2PerKm ?? fake()->randomFloat(2, 10, 100),
        ]);
    }
}