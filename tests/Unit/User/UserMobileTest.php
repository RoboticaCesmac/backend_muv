<?php

namespace Tests\Unit\User;

use App\Models\Vehicle;
use Database\Factories\TokenFactory;
use Database\Factories\UserFactory;
use Tests\TestCase;

class UserMobileTest extends TestCase
{
    protected string $url;

    public function setUp(): void
    {
        parent::setUp();

        $this->url = '/api/v1/mobile/user/';
    }

    public function test_reset_password(): void
    {
        $user = UserFactory::new()->create();

        $token = TokenFactory::new()->stateEmail($user->email)->stateToken('123456')->create();
            
        $form = [
            'email' => $user->email,
            'token' => '123456',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->jsonAsUser('POST', '/api/v1/auth/reset-password', $form, $user);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Senha alterada com sucesso',
        ]);
    }

    public function test_me_with_user_in_first_login(): void
    {
        $user = UserFactory::new()
        ->stateVehicleId(Vehicle::VEHICLES['gasoline_car'])
        ->stateIsFirstLogin(false)
        ->stateAvatar(2)
        ->create();

        $response = $this->jsonAsUser('GET', '/api/v1/auth/me', [], $user);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'user_name',
            'email',
            'date_of_birth',
            'gender',
            'total_points',
            'total_km_driven',
            'total_carbon_footprint',
            'is_first_login',
            'avatar' => [
                'id',
                'name',
                "avatar_path",
                "is_default",
                "created_at",
                "updated_at",
                "avatar_url",
            ],
            'vehicle' => [
                'id',
                'name',
                'co2_per_km',
                'points_per_km',
                'icon_path',
                'icon_url',
            ],
            'perfil_data' => [
                'current_level',
                'carbon_footprint_to_next_level',
                'total_points',
                'total_carbon_footprint',
                'total_carbon_footprint_of_next_level',
                'distance_traveled',
                'current_level_url',
            ],
        ]);
    }

    public function test_me_with_period_filter_month(): void
    {
        $user = UserFactory::new()
            ->stateVehicleId(Vehicle::VEHICLES['gasoline_car'])
            ->stateIsFirstLogin(false)
            ->stateAvatar(2)
            ->create();

        $response = $this->jsonAsUser('GET', '/api/v1/auth/me?period=month', [], $user);

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'perfil_data' => [
                'current_level',
                'carbon_footprint_to_next_level',
                'total_points',
                'total_carbon_footprint',
                'total_carbon_footprint_of_next_level',
                'distance_traveled',
                'current_level_url',
            ],
        ]);
    }

    public function test_me_with_period_filter_all(): void
    {
        $user = UserFactory::new()
            ->stateVehicleId(Vehicle::VEHICLES['gasoline_car'])
            ->stateIsFirstLogin(false)
            ->stateAvatar(2)
            ->create();

        $response = $this->jsonAsUser('GET', '/api/v1/auth/me?period=all', [], $user);

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'perfil_data' => [
                'current_level',
                'carbon_footprint_to_next_level',
                'total_points',
                'total_carbon_footprint',
                'total_carbon_footprint_of_next_level',
                'distance_traveled',
                'current_level_url',
            ],
        ]);
    }

    public function test_me_without_period_uses_default_behavior(): void
    {
        $user = UserFactory::new()
            ->stateVehicleId(Vehicle::VEHICLES['gasoline_car'])
            ->stateIsFirstLogin(false)
            ->stateAvatar(2)
            ->create();

        $response = $this->jsonAsUser('GET', '/api/v1/auth/me', [], $user);

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'perfil_data' => [
                'current_level',
                'carbon_footprint_to_next_level',
                'total_points',
                'total_carbon_footprint',
                'total_carbon_footprint_of_next_level',
                'distance_traveled',
                'current_level_url',
            ],
        ]);    
    }
}