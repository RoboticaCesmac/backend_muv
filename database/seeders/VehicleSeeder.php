<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        Vehicle::factory()->state([
            'name' => 'Carro à gasolina',
            'co2_per_km' => 0.19,
            'icon_path' => 'images/vehicles/CarImage.png',
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Carro à diesel',
            'co2_per_km' => 0.18,
            'icon_path' => 'images/vehicles/CarImage.png',
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Bicicleta',
            'co2_per_km' => 0.016,
            'icon_path' => 'images/vehicles/BikeImage.png',
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Caminhada',
            'co2_per_km' => 0.03,
            'icon_path' => 'images/vehicles/WalkingImage.png',
        ])->create();

        Vehicle::factory()->state([
            'name' => 'Ônibus',
            'co2_per_km' => 0.045,
            'icon_path' => 'images/vehicles/BusImage.png',
        ])->create();
    }
}