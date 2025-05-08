<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Vehicle\VehicleUrlResource;
use App\Http\Resources\UserAvatar\UserAvatarUrlResource;

class MobileUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'total_points' => $this->total_points,
            'total_km_driven' => $this->total_km_driven,
            'total_carbon_footprint' => $this->total_carbon_footprint,
            'is_first_login' => $this->is_first_login,
            'vehicle' => $this->when($this->vehicle, new VehicleUrlResource($this->vehicle)),
            'avatar' => $this->when($this->avatar, new UserAvatarUrlResource($this->avatar)),
            'perfil_data' => $this->perfil_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 