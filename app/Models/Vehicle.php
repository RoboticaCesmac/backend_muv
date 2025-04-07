<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
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
    ];

    protected $casts = [
        'co2_per_km' => 'float',
    ];

    /**
     * Get the routes associated with the vehicle.
     */
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }
} 