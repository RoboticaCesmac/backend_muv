<?php

namespace Database\Factories;

use App\Models\RoutePoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoutePointFactory extends Factory
{
    protected $model = RoutePoint::class;

    public function definition()
    {
        return [
            'route_id' => RouteFactory::new()->create()->id,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }

    public function stateRouteId(?int $routeId = null)
    {
        return $this->state(fn (array $attributes) => [
            'route_id' => $routeId ?? RouteFactory::new()->create()->id,
        ]);
    }

    public function stateLatitude(?float $latitude = null)
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => $latitude ?? $this->faker->latitude,
        ]);
    }

    public function stateLongitude(?float $longitude = null)
    {
        return $this->state(fn (array $attributes) => [
            'longitude' => $longitude ?? $this->faker->longitude,
        ]);
    }
}