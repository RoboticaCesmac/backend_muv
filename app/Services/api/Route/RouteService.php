<?php

namespace App\Services\api\Route;

use App\Enums\RouteStatusEnum;
use App\Exceptions\Route\RouteNotFoundException;
use App\Models\Route;
use App\Models\RoutePoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RouteService
{
    private Route $route;
    
    private const GASOLINE_CAR_EMISSION_FACTOR = 0.19;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function getPaginated(Request $request)
    {
        $user = Auth::user();

        $perPage = 4;
        $page = $request->input('page', 1);

        return $this->route
            ->where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::Completed))
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->where('user_id', $user->id)
            ->take($perPage)
            ->get();
    }

    public function start(array $data): Route
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            $openRoutes = $this->route->newQuery()
                ->where('user_id', $user->id)
                ->where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::InProgress))
                ->with('routePoints', 'vehicle')
                ->get();

            $this->closeOpenedRoutes($openRoutes, $user);

            $newRoute = $this->route->create([
                'user_id'         => $user->id,
                'vehicle_id'      => $data['vehicle_id'],
                'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::InProgress),
                'started_at'      => now(),
            ]);

            $newRoute->routePoints()->create([
                'route_id'  => $newRoute->id,
                'latitude'  => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);

            return $newRoute;
        });
    }

    public function finish(array $data): Route
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            $route = $this->route
                ->where('user_id', $user->id)
                ->where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::InProgress))
                ->first();

            if (! $route) {
                throw new RouteNotFoundException('Rota não encontrada');
            }

            $route->routePoints()->create($data);
            $points = $route->routePoints()
                ->orderBy('created_at')
                ->get()
                ->values()
                ->all();

            $distanceKm = $this->calculateDistance($points);
            $vehicle = $route->vehicle;
            $carbonFootprint = $this->calculateCarbonFootprint($distanceKm, $vehicle);
            $pointsCalc = $distanceKm * ($vehicle->points_per_km ?? 0);
            
            $endedAt = now();
            $startedAt = $route->started_at;
            $hoursElapsed = $startedAt->diffInSeconds($endedAt) / 3600;
            $velocityAverage = $hoursElapsed > 0 ? $distanceKm / $hoursElapsed : 0;
            
            $carbonProduced = $distanceKm * $vehicle->co2_per_km;
            $route->update([
                'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::Completed),
                'ended_at'        => $endedAt,
                'distance_km'     => $distanceKm,
                'carbon_footprint' => $carbonFootprint,
                'points'          => round($pointsCalc, 2),
                'velocity_average' => round($velocityAverage, 2),
                'carbon_produced' => $carbonProduced,
            ]);
            
            $this->updateUserStats($user, $distanceKm, round($pointsCalc, 2), $carbonFootprint);

            return $route->fresh();
        });
    }

    public function points(array $data): RoutePoint
    {
        $user = Auth::user();

        $route = $this->route
            ->where('user_id', $user->id)
            ->where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::InProgress))
            ->first();

        if (! $route || $route->user_id !== $user->id) {
            throw new RouteNotFoundException('Rota não encontrada');
        }

        $routePoint = $route->routePoints()->create($data);

        return $routePoint;
    }

    /**
     * Calcula a distância entre pontos geográficos usando a fórmula de Haversine
     * 
     * @param array $points Array de pontos com latitude e longitude
     * @return float Distância em quilômetros
     */
    private function calculateDistance(array $points): float
    {
        $earthRadius = 6371;
        $totalDistance = 0.0;

        if (count($points) > 1) {
            foreach ($points as $i => $pt) {
                if ($i === 0) {
                    continue;
                }
                $prev = $points[$i - 1];
                $latFrom = deg2rad($prev->latitude);
                $lonFrom = deg2rad($prev->longitude);
                $latTo = deg2rad($pt->latitude);
                $lonTo = deg2rad($pt->longitude);
                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(
                    pow(sin($latDelta / 2), 2) +
                    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
                ));

                $totalDistance += $earthRadius * $angle;
            }
        }

        return round($totalDistance, 2);
    }

    /**
     * Calcula a pegada de carbono economizada em relação a um carro a gasolina
     * 
     * @param float $distanceKm Distância em quilômetros
     * @param object $vehicle Veículo utilizado
     * @return float Pegada de carbono economizada em kg de CO2
     */
    private function calculateCarbonFootprint(float $distanceKm, $vehicle): float
    {
        $gasolineCarEmission = $distanceKm * self::GASOLINE_CAR_EMISSION_FACTOR;
        
        $actualEmission = $distanceKm * ($vehicle->co2_per_km ?? 0);
        
        $carbonSaved = $gasolineCarEmission - $actualEmission;
        
        return round($carbonSaved, 2);
    }

    /**
     * Atualiza as estatísticas totais do usuário após a conclusão de uma rota
     * 
     * @param User $user Usuário a ser atualizado
     * @param float $distanceKm Distância percorrida em km
     * @param float $points Pontos ganhos na rota
     * @param float $carbonFootprint Pegada de carbono economizada
     * @return void
     */
    private function updateUserStats(User $user, float $distanceKm, float $points, float $carbonFootprint): void
    {
        $user->total_km_driven = ($user->total_km_driven ?? 0) + $distanceKm;
        $user->total_points = ($user->total_points ?? 0) + $points;
        $user->total_carbon_footprint = ($user->total_carbon_footprint ?? 0) + $carbonFootprint;
        
        $user->save();
    }

    private function closeOpenedRoutes(Collection $openRoutes, User $user): void
    {
        foreach ($openRoutes as $oldRoute) {

            $points = $oldRoute->routePoints()
                ->orderBy('created_at')
                ->get();
            
            $pointsCollection = $points->values()->all();

            $distanceKm = $this->calculateDistance($pointsCollection);
            $vehicle = $oldRoute->vehicle;
            $carbonFootprint = $this->calculateCarbonFootprint($distanceKm, $vehicle);
            $pointsCalc = $distanceKm * ($vehicle->points_per_km ?? 0);
            
            $endedAt = now();
            $startedAt = $oldRoute->started_at;
            $hoursElapsed = $startedAt->diffInSeconds($endedAt) / 3600;
            $velocityAverage = $hoursElapsed > 0 ? $distanceKm / $hoursElapsed : 0;

            $oldRoute->update([
                'route_status_id' => RouteStatusEnum::getId(RouteStatusEnum::Completed),
                'ended_at'        => $endedAt,
                'distance_km'     => $distanceKm,
                'carbon_footprint' => $carbonFootprint,
                'points'          => round($pointsCalc, 2),
                'velocity_average' => round($velocityAverage, 2),
            ]);
            
            $this->updateUserStats($user, $distanceKm, round($pointsCalc, 2), $carbonFootprint);
        }
    }
}
