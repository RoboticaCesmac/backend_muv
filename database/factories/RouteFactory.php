<?php

namespace Database\Factories;

use App\Enums\RouteStatusEnum;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new()->create()->id,
            'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::InProgress),
            'vehicle_id' => Vehicle::VEHICLES['gasoline_car'],
            'points' => fake()->numberBetween(1, 100),
            'carbon_footprint' => fake()->numberBetween(1, 100),
            'distance_km' => fake()->numberBetween(1, 100),
            'started_at' => fake()->dateTime(),
            'ended_at' => fake()->dateTime(),
        ];
    }

    public function stateUserId(?int $userId = null) {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId ?? UserFactory::new()->create()->id,
        ]);
    }

    public function stateRouteStatusId(?int $routeStatusId = null) {
        return $this->state(fn (array $attributes) => [
            'route_status_id' => $routeStatusId ?? RouteStatusFactory::new()->create()->id,
        ]);
    }

    public function stateVehicleId(?int $vehicleId = null) {
        return $this->state(fn (array $attributes) => [
            'vehicle_id' => $vehicleId ?? VehicleFactory::new()->create()->id,
        ]);
    }

    public function statePoints(?int $points = null) {
        return $this->state(fn (array $attributes) => [
            'points' => $points ?? fake()->numberBetween(1, 100),
        ]);
    }

    public function stateCo2Produced(?int $co2Produced = null) {
        return $this->state(fn (array $attributes) => [
            'co2_produced' => $co2Produced ?? fake()->numberBetween(1, 100),
        ]);
    }

    public function stateDistanceKm(?int $distanceKm = null) {
        return $this->state(fn (array $attributes) => [
            'distance_km' => $distanceKm ?? fake()->numberBetween(1, 100),
        ]);
    }
}
