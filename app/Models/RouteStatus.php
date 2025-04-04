<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteStatus extends Model
{
    use HasFactory;

    protected $table = 'route_status';

    protected $fillable = [
        'descricao',
    ];

    /**
     * Get the routes with this status.
     */
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }
} 