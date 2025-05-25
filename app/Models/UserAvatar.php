<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAvatar extends BaseModel
{
    protected $fillable = ['name', 'avatar_path', 'is_default'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
