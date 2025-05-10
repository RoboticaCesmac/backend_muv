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
use Illuminate\Support\Facades\URL;

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
        'total_carbon_footprint',
        'total_km_driven',
        'total_points',
        'is_first_login',
        'email_verified_at',
        'vehicle_id',
        'avatar_id',
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
        'total_km_driven' => 'decimal:2',
        'total_carbon_footprint' => 'decimal:2',
        'total_points' => 'decimal:2',
    ];

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(UserAvatar::class);
    }

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

    public function getPerfilDataAttribute(): array
    {
        // ta om filtro de 1 mes.
        $routes = $this->routes()->where('created_at', '>=', now()->subMonth())->get();

        $levels = UserLevel::orderBy('carbon_footprint_required', 'asc')->get();

        $totalCarbonFootprint = round($routes->sum('carbon_footprint'), 2) ?? 0;

        $totalPoints = round($routes->sum('points'), 2) ?? 0;

        $distanceTraveled = round($routes->sum('distance_km'), 2) ?? 0;

        $currentLevel = $levels->firstWhere('carbon_footprint_required', '<=', $totalCarbonFootprint);

        $nextLevel = $levels->firstWhere('carbon_footprint_required', '>', $totalCarbonFootprint);

        $carbonFootprintToNextLevel = $nextLevel ? $nextLevel->carbon_footprint_required - $totalCarbonFootprint : 0;

        $totalCarbonFootprintOfNextLevel = $nextLevel ? $nextLevel->carbon_footprint_required : $currentLevel->carbon_footprint_required;

        return [
            'current_level' => $currentLevel->level_number,
            'carbon_footprint_to_next_level' => $carbonFootprintToNextLevel,
            'total_points' => $totalPoints,
            'total_carbon_footprint' => $totalCarbonFootprint,
            'total_carbon_footprint_of_next_level' => $totalCarbonFootprintOfNextLevel,
            'distance_traveled' => $distanceTraveled,
            'current_level_url' => URL::asset($currentLevel->icon_path),
        ];
    }
}
