<?php

namespace App\Http\Resources\Route;

use App\Http\Resources\Vehicle\VehicleUrlResource;
use Illuminate\Http\Resources\Json\JsonResource;
use DateTimeInterface;

class RouteIndexResource extends JsonResource
{
    protected function serializeDateTime($date)
    {
        return $date->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('Y-m-d\TH:i:s.uP');
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'route_status_id' => $this->route_status_id,
            'vehicle_id' => $this->vehicle_id,
            'points' => $this->points,
            'carbon_footprint' => $this->carbon_footprint,
            'velocity_average' => $this->velocity_average,
            'distance_km' => $this->distance_km,
            'started_at' => $this->serializeDateTime($this->started_at),
            'ended_at' => $this->serializeDateTime($this->ended_at),
            'created_at' => $this->serializeDateTime($this->created_at),
            'updated_at' => $this->serializeDateTime($this->updated_at),
            'route_points' => $this->whenLoaded('routePoints', $this->routePoints->sortByDesc('created_at')),
            'vehicle' => $this->whenLoaded('vehicle', new VehicleUrlResource($this->vehicle)),
        ];
    }
}