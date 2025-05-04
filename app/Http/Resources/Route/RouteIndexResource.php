<?php

namespace App\Http\Resources\Route;

use App\Http\Resources\Vehicle\VehicleUrlResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteIndexResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'route_status_id' => $this->route_status_id,
            'vehicle_id' => $this->vehicle_id,
            'points' => $this->points,
            'co2_produced' => $this->co2_produced,
            'distance_km' => $this->distance_km,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'route_points' => $this->whenLoaded('routePoints', $this->routePoints),
            'vehicle'      => $this->whenLoaded('vehicle', new VehicleUrlResource($this->vehicle)),
        ];
    }
}