<?php

namespace App\Http\Controllers\api\Vehicle;

use App\Services\api\Vehicle\VehicleService;
use Illuminate\Routing\Controller;
use App\Http\Resources\Vehicle\VehicleUrlResource;

class VehicleMobileController extends Controller
{
    private $vehicleService;
    
    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function all()
    {
        $vehicles = $this->vehicleService->all();

        return response()->json([
            'data' => VehicleUrlResource::collection($vehicles)
        ]);
    }
}