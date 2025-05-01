<?php

namespace App\Services\api\Route;

use App\Enums\RouteStatusEnum;
use App\Exceptions\Route\RouteNotFoundException;
use App\Models\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RouteMobileService
{
    private Route $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function start(array $data) : Route
    {
        return DB::transaction(function() use ($data) {
            $user = Auth::user();

            $this->route
                ->where('user_id', $user->id)
                ->whereIn('route_status_id', [
                    RouteStatusEnum::getId(RouteStatusEnum::InProgress),
                ])
                ->update([
                    'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::Completed),
                ]);

            $route = $this->route->create([
                'user_id' => $user->id,
                'vehicle_id' => $data['vehicle_id'],
                'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::InProgress),
                'started_at' => now(),
            ]);

            $route->routePoints()->create([
                'route_id' => $route->id,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);

            return $route;
        });
    }

    public function finish(array $data): Route
    {
        return DB::transaction(function() use ($data) {
            $userId = Auth::id();

            $route = $this->route->newQuery()
                ->where('user_id', $userId)
                ->where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::InProgress))
                ->first();

            if (! $route) {
                throw new RouteNotFoundException('Rota não encontrada');
            }

            $route->routePoints()->create([
                'latitude'  => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);

            $pointsOrdered = $route->routePoints()
                ->orderBy('created_at')
                ->get(['latitude', 'longitude']);

            $earthRadius = 6371;
            $distance = 0.0;

            foreach ($pointsOrdered as $index => $point) {
                if ($index === 0) {
                    continue;
                }
                $prev = $pointsOrdered[$index - 1];

                $latFrom = deg2rad($prev->latitude);
                $lonFrom = deg2rad($prev->longitude);
                $latTo   = deg2rad($point->latitude);
                $lonTo   = deg2rad($point->longitude);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(
                    pow(sin($latDelta / 2), 2)
                    + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
                ));

                $distance += $earthRadius * $angle;
            }

            $roundedDistance = round($distance, 2);
            $vehicle = $route->vehicle;

            $co2Produced = $roundedDistance * ($vehicle->co2_per_km ?? 0);
            $points = $roundedDistance * ($vehicle->points_per_km ?? 0);
            
            $route->update([
                'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::Completed),
                'ended_at'        => now(),
                'distance_km'     => $roundedDistance,
                'co2_produced'    => round($co2Produced, 2),
                'points'          => round($points, 2),
            ]);

            return $route->fresh();
        });
    }

    public function points(Array $data) : Route
    {
        $user = Auth::user();

        $route = $this->route
            ->where('user_id', $user->id)
            ->where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::InProgress))
            ->first();

        if (! $route) {
            throw new RouteNotFoundException('Rota não encontradas');
        }

        if ($route->user_id !== $user->id) {
            throw new RouteNotFoundException('Rota não encontradas');
        }

        $route->routePoints()->create([
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        return $route;
    }
}
