<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class RoutePoint extends BaseModel
{
    use HasFactory;

    protected $table = 'route_point';

    protected $primaryKey = 'id';

    protected $fillable = [
        'route_id',
        'latitude',
        'longitude',
        'created_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Get the route that owns the point.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
} 