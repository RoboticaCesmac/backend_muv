<?php

namespace App\Http\Controllers\web;

use Illuminate\Routing\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleController extends Controller
{
    /**
     * Display the vehicles listing page
     */
    public function index()
    {
        $vehicles = Vehicle::all();
        
        return Inertia::render('Vehicles', [
            'vehicles' => $vehicles,
            'title' => 'Veículos'
        ]);
    }

    /**
     * Update the points_per_km of a vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'points_per_km' => 'required|numeric|min:0',
        ]);

        $vehicle->points_per_km = $validated['points_per_km'];
        $vehicle->save();

        return response()->json([
            'message' => 'Pontos por km atualizados com sucesso',
            'vehicle' => $vehicle
        ]);
    }
} 