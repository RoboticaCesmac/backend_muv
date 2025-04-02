<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubjectContract;

class User extends Authenticatable implements JWTSubjectContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
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
    ];

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
}
