<?php

namespace Tests\Unit\Vehicle;

use Database\Factories\UserFactory;
use Tests\TestCase;

class VehicleMobileTest extends TestCase
{
    private string $url;
    public function setUp(): void
    {
        parent::setUp();

        $this->url = '/api/v1/mobile/vehicle';
    }

    public function test_index(): void
    {
        $user = UserFactory::new()->create();

        $response = $this->jsonAsUser('GET', $this->url, [], $user);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'co2_per_km',
                'icon_path'
            ],
        ]);
    }
}