<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'latitude',
        'longitude',
        'timestamp',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'timestamp' => 'datetime',
    ];

    /**
     * Get the route that owns the point.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
} 