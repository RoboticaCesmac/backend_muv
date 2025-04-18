<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_number',
        'points_required',
        'icon_path',
        'is_default'
    ];
    
    /**
     * Get users with this level.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}