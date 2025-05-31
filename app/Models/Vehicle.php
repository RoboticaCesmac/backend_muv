<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class Vehicle extends BaseModel
{
    use HasFactory;

    const VEHICLES = [
        'gasoline_car' => 1,
        'diesel_car' => 2,
        'bicycle' => 3,
        'walking' => 4,
        'bus' => 5,
    ];

    protected $fillable = [
        'name',
        'co2_per_km',
        'icon_path',
        'points_per_km',
    ];

    protected $casts = [
        'co2_per_km' => 'decimal:3',
        'points_per_km' => 'decimal:2',
    ];

    /**
     * Get the routes associated with the vehicle.
     */
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }
} 