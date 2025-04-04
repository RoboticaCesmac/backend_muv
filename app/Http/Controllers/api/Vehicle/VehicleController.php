<?php

namespace App\Http\Controllers\api\Vehicle;

use App\Services\api\Vehicle\VehicleService;
use Illuminate\Routing\Controller;

class VehicleController extends Controller
{
    private $vehicleService;
    
    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function all()
    {
        return response()->json($this->vehicleService->all());
    }
}