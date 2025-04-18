<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubjectContract;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements JWTSubjectContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'email',
        'password',
        'gender',
        'date_of_birth',
        'total_points',
        'total_km',
        'is_first_login',
        'email_verified_at',
        'vehicle_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'date_of_birth' => 'date',
        'is_first_login' => 'boolean',
        'vehicle_id' => 'integer',
        'user_level_id' => 'integer',
        'total_km' => 'decimal:2',
        'total_points' => 'integer',
    ];

    /**
     * Get the vehicle that belongs to user.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the routes associated with the user.
     */
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get all tokens for the user.
     */
    public function tokens(): MorphMany
    {
        return $this->morphMany(Token::class, 'tokenable');
    }

    /**
     * Get the user's email verification token.
     */
    public function emailVerificationToken(): MorphMany
    {
        return $this->morphMany(Token::class, 'tokenable')
            ->where('type', 'email_verification');
    }

    /**
     * Get the user's password reset token.
     */
    public function passwordResetToken(): MorphMany
    {
        return $this->morphMany(Token::class, 'tokenable')
            ->where('type', 'password_reset');
    }

    /**
     * Get the user's active tokens.
     */
    public function activeTokens(): MorphMany
    {
        return $this->morphMany(Token::class, 'tokenable')
            ->where('expires_at', '>', now());
    }

    /**
     * Check if the logged in user can edit this user
     *
     * @return bool
     */
    public function canEdit(): bool
    {
        $loggedUser = Auth::user();
        
        return $loggedUser && $loggedUser->is_admin;
    }

    /**
     * Check if the logged in user can delete this user
     *
     * @return bool
     */
    public function canDelete(): bool
    {
        $loggedUser = Auth::user();

        return $loggedUser && $loggedUser->is_admin;
    }

    /**
     * Check if the logged in user can update this user
     *
     * @return bool
     */
    public function canUpdate(): bool
    {
        $loggedUser = Auth::user();

        return $loggedUser && $loggedUser->is_admin;
    }

    /**
     * Check if this is the user's first login
     *
     * @return bool
     */
    public function isFirstLogin(): bool
    {
        return $this->is_first_login;
    }
}
