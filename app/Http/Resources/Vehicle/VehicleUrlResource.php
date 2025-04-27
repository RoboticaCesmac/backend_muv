<?php

namespace App\Http\Resources\Vehicle;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class VehicleUrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'co2_per_km' => $this->co2_per_km,
            'icon_path' => $this->icon_path,
            'points_per_km' => $this->points_per_km,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'icon_url' => URL::asset($this->icon_path),
        ];
    }
} 