<?php

namespace Database\Seeders;

use App\Enums\RouteStatusEnum;
use App\Models\RouteStatus;
use Illuminate\Database\Seeder;

class RouteStatusSeeder extends Seeder
{
    public function run()
    {
        foreach (RouteStatusEnum::cases() as $status) {
            RouteStatus::create([
                'description' => $status->value
            ]);
        }
    }
}