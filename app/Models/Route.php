<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'route_status_id',
        'vehicle_id',
        'points',
        'co2_produced',
        'distance_km',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'co2_produced' => 'float',
        'distance_km' => 'float',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the user that owns the route.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status of the route.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(RouteStatus::class, 'route_status_id');
    }

    /**
     * Get the vehicle used for the route.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the points recorded for this route.
     */
    public function routePoints(): HasMany
    {
        return $this->hasMany(RoutePoint::class);
    }
} 