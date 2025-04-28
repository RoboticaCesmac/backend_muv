<?php

namespace Database\Factories;

use App\Enums\RouteStatusEnum;
use App\Models\Route;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('teste'),
            'is_admin' => false,
            'is_first_login' => true,
            'total_km' => 0,
            'total_points' => 0,
            'date_of_birth' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female']),
            'total_km' => 0,
            'total_points' => 0,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    
    public function stateUserName(?string $userName = null) {
        return $this->state(fn (array $attributes) => [
            'user_name' => $userName ?? fake()->name(),
        ]);
    }

    public function stateEmail(?string $email = null) {
        return $this->state(fn (array $attributes) => [
            'email' => $email ?? fake()->unique()->safeEmail(),
        ]);
    }

    public function stateIsAdmin(?bool $isAdmin = null) {
        return $this->state(fn (array $attributes) => [
            'is_admin' => $isAdmin ?? false,
        ]);
    }

    public function statePassword(?string $password = null) {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make($password ?? 'teste'),
        ]);
    }

    public function stateIsFirstLogin(?bool $isFirstLogin = null) {
        return $this->state(fn (array $attributes) => [
            'is_first_login' => $isFirstLogin ?? true,
        ]);
    }

    public function stateVehicleId(?int $vehicleId = null) {
        return $this->state(fn (array $attributes) => [
            'vehicle_id' => $vehicleId ?? VehicleFactory::new()->create()->id,
        ]);
    }

    public function stateAvatar(?string $avatar = null) {
        return $this->state(fn (array $attributes) => [
            'avatar_id' => $avatar ?? UserAvatarFactory::new()->create()->id,
        ]);
    }

    public function stateRouteFinalizada() {
       return $this->afterCreating(function (User $user) {
           Route::factory()->create([
               'user_id' => $user->id,
               'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::Completed)
           ]);
        });
    }
}
