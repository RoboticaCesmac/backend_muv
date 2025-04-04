<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'co2_per_km',
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