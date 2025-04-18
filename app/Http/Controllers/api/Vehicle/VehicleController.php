<?php

namespace App\Http\Controllers\api\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Get all vehicles
     */
    public function index()
    {
        $vehicles = Vehicle::all();
        return response()->json($vehicles);
    }

    /**
     * Update the points_per_km of a vehicle
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'points_per_km' => 'required|numeric|min:0',
        ]);

        $vehicle = Vehicle::findOrFail($id);
        $vehicle->points_per_km = $request->points_per_km;
        $vehicle->save();

        return response()->json($vehicle);
    }
} 