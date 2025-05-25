<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends BaseModel
{
    use HasFactory;

    protected $table = 'routes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'route_status_id',
        'vehicle_id',
        'points',
        'carbon_footprint',
        'distance_km',
        'velocity_average',
        'started_at',
        'ended_at',
        'carbon_produced',
    ];

    protected $casts = [
        'carbon_footprint' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'points' => 'decimal:2',
        'velocity_average' => 'decimal:2',
        'started_at' => 'datetime:Y-m-d H:i:s',
        'ended_at' => 'datetime:Y-m-d H:i:s',
        'carbon_produced' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
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