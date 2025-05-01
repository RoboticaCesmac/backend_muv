<?php

namespace Tests\Unit\Route;

use App\Enums\RouteStatusEnum;
use App\Models\Route;
use App\Models\Vehicle;
use Database\Factories\RouteFactory;
use Database\Factories\RoutePointFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RouteControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected string $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->url = '/api/v1/mobile/route';
    }

    public function test_index_route()
    {
        $user = UserFactory::new()->create();

        $routes = Route::where('user_id', $user->id)->get();

        $response = $this->jsonAsUser('GET', $this->url , [], $user); 

        $response->assertStatus(200);

        $response->assertJson([
            'data' => $routes->toArray(),
        ]);
    }

    public function test_start_route()
    {
        $user = UserFactory::new()->create();

        $form = [
            'vehicle_id' => Vehicle::VEHICLES['gasoline_car'],
            'latitude'  => -23.550520,
            'longitude' => -46.633308,
        ];

        $response = $this->jsonAsUser('POST', $this->url . '/start', $form, $user);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Rota Iniciada com sucesso',
        ]);
    }

    public function test_insert_route_point()
    {
        $user = UserFactory::new()->create();

        $route = RouteFactory::new()->stateUserId($user->id)->create();

        $form = [
            'latitude'  => -23.550520,
            'longitude' => -46.633308,
        ];

        $response = $this->jsonAsUser('POST', $this->url . "/points", $form, $user);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Ponto adicionado com sucesso',
        ]);
    }

    public function test_finish_route()
    {
        $user = UserFactory::new()->create();
        $route = RouteFactory::new()->stateUserId($user->id)->create();

        $points = [
            ['latitude' => -23.550520, 'longitude' => -46.633308],
            ['latitude' => -23.546520, 'longitude' => -46.633308], // ~444m ao norte
            ['latitude' => -23.541520, 'longitude' => -46.633308], // ~928m ao norte (total ~1km)
        ];
        
        foreach ($points as $point) {
            RoutePointFactory::new()
                ->stateRouteId($route->id)
                ->stateLatitude($point['latitude'])
                ->stateLongitude($point['longitude'])
                ->create();
        }

        $lastPoint = end($points);

        $form = [
            'latitude'  => $lastPoint['latitude'],
            'longitude' => $lastPoint['longitude'],
        ];

        $response = $this->jsonAsUser('POST', $this->url . '/finish', $form, $user);

        $response->assertStatus(200);

        $route->refresh();

        $response->assertJson([
            'message' => 'Rota Finalizada com sucesso',
            'data' => [
                'id' => $route->id,
                'user_id' => $user->id,
                'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::Completed),
                'vehicle_id' => $route->vehicle_id,
                'points' => 1,
                'co2_produced' => 0.19,
                'distance_km' => 1,
                'started_at' => $route->started_at->toDateTimeString(),
                'ended_at' => $route->ended_at->toDateTimeString(),
            ],
        ]);
    }

    public function test_finish_route_with_1_5_km()
    {
        $user = UserFactory::new()->create();
        $route = RouteFactory::new()->stateUserId($user->id)->create();

        $points = [
            ['latitude' => -23.550520, 'longitude' => -46.633308],
            ['latitude' => -23.546020, 'longitude' => -46.633308], // ~500m ao norte
            ['latitude' => -23.541520, 'longitude' => -46.633308], // ~1km total
            ['latitude' => -23.537020, 'longitude' => -46.633308], // ~1.5km total
        ];
        
        foreach ($points as $point) {
            RoutePointFactory::new()
                ->stateRouteId($route->id)
                ->stateLatitude($point['latitude'])
                ->stateLongitude($point['longitude'])
                ->create();
        }

        $lastPoint = end($points);

        $form = [
            'latitude'  => $lastPoint['latitude'],
            'longitude' => $lastPoint['longitude'],
        ];

        $response = $this->jsonAsUser('POST', $this->url . '/finish', $form, $user);

        $response->assertStatus(200);

        $route->refresh();

        $response->assertJson([
            'message' => 'Rota Finalizada com sucesso',
            'data' => [
                'id' => $route->id,
                'user_id' => $user->id,
                'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::Completed),
                'vehicle_id' => $route->vehicle_id,
                'points' => 1.5,
                'co2_produced' => 0.29,
                'distance_km' => 1.5,
                'started_at' => $route->started_at->toDateTimeString(),
                'ended_at' => $route->ended_at->toDateTimeString(),
            ],
        ]);
    }
}