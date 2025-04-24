<?php

namespace Database\Factories;

use App\Models\RouteStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteStatusFactory extends Factory
{
    protected $model = RouteStatus::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->word,
        ];
    }

    public function stateDescription(?string $description = null) {
        return $this->state([
            'description' => $description ?? $this->faker->word,
        ]);
    }
}
