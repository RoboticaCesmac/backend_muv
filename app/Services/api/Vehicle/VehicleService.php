<?php

namespace App\Services\api\Vehicle;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

class VehicleService
{
    public function all(): Collection
    {
        return Vehicle::all();
    }
}