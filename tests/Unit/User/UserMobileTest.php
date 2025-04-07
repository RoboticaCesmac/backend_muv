<?php

namespace Tests\Unit\User;

use App\Models\Vehicle;
use Carbon\Carbon;
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

    public function test_first_login(): void
    {
        $user = UserFactory::new()->create();

        $form = [
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'user_name' => 'John Doe',
            'vehicle_id' => Vehicle::VEHICLES['gasoline_car'],
        ];

        $response = $this->jsonAsUser('POST', $this->url . $user->id . '/first-login', $form, $user);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Primeiro login realizado com sucesso',
            'data' => [
                'id' => $user->id,
                'user_name' => $form['user_name'],
                'email' => $user->email,
                'date_of_birth' => Carbon::parse($form['date_of_birth'])->toISOString(),
                'gender' => $form['gender'],
                'vehicle_id' => $form['vehicle_id'],
            ],
        ]);
    }
}