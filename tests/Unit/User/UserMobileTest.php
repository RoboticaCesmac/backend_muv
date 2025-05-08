<?php

namespace Tests\Unit\User;

use App\Models\UserAvatar;
use App\Models\Vehicle;
use Carbon\Carbon;
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

    // public function test_first_login(): void
    // {
    //     $user = UserFactory::new()->create();

    //     $form = [
    //         'date_of_birth' => '1990-01-01',
    //         'gender' => 'male',
    //         'user_name' => 'John Doe',
    //         'vehicle_id' => Vehicle::VEHICLES['gasoline_car'],
    //     ];

    //     $response = $this->jsonAsUser('POST', $this->url . '/first-login', $form, $user);
    //     dd($response);
    //     $response->assertStatus(200);

    //     $response->assertJson([
    //         'message' => 'Primeiro login realizado com sucesso',
    //         'data' => [
    //             'id' => $user->id,
    //             'user_name' => $form['user_name'],
    //             'email' => $user->email,
    //             'date_of_birth' => Carbon::parse($form['date_of_birth'])->toISOString(),
    //             'gender' => $form['gender'],
    //             'vehicle_id' => $form['vehicle_id'],
    //         ],
    //     ]);
    // }

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
}