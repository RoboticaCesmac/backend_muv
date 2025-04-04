<?php

namespace App\Services\api\Vehicle;

use App\Models\Vehicle;

class VehicleService
{
    public function all()
    {
        return Vehicle::all();
    }
}