<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class UserLevel extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'level_number',
        'carbon_footprint_required',
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