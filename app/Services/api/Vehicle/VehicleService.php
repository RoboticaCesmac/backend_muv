<?php

namespace App\Services\api\Vehicle;

use App\Models\Vehicle;

class VehicleService
{
    public function all()
    {
        return Vehicle::all()->map(function ($vehicle) {
            $filePath = public_path($vehicle->icon_path);

            $imageData = file_exists($filePath) ? base64_encode(file_get_contents($filePath)) : null;
            
            $vehicle->icon_base64 = $imageData ? 'data:image/png;base64,' . $imageData : null;

            return $vehicle;
        });
    }
}