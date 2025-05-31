<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        Vehicle::factory()->state([
            'name' => 'Gasolina',
            'co2_per_km' => 0.19,
            'icon_path' => 'images/vehicles/CarImage.png',
            'points_per_km' => 1.0,
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Diesel',
            'co2_per_km' => 0.18,
            'icon_path' => 'images/vehicles/CarImage.png',
            'points_per_km' => 1.0,
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Bicicleta',
            'co2_per_km' => 0.016,
            'icon_path' => 'images/vehicles/BikeImage.png',
            'points_per_km' => 1.0,
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Caminhada',
            'co2_per_km' => 0.03,
            'icon_path' => 'images/vehicles/WalkingImage.png',
            'points_per_km' => 1.0,
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Ônibus',
            'co2_per_km' => 0.045,
            'icon_path' => 'images/vehicles/BusImage.png',
            'points_per_km' => 1.0,
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Motocicleta',
            'co2_per_km' => 0.07,
            'icon_path' => 'images/vehicles/MotoImage.png',
            'points_per_km' => 1.0,
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Metrô',
            'co2_per_km' => 0.02,
            'icon_path' => 'images/vehicles/TrainImage.png',
            'points_per_km' => 1.0,
        ])->create();
    }
}