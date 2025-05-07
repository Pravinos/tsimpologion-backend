<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasImages;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasImages;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'images',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'images' => 'array',
        ];
    }

    /**
     * Get the food spots owned by the user.
     */
    public function foodSpots(): HasMany
    {
        return $this->hasMany(FoodSpot::class, 'owner_id');
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

     /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

     /**
     * Check if user is a spot owner.
     */
    public function isSpotOwner(): bool
    {
        return $this->role === 'spot_owner';
    }

    /**
     * Check if user is a foodie.
     */
    public function isFoodie(): bool
    {
        return $this->role === 'foodie';
    }

     // Users can only manage their own images
     public function userCanManageImages($user): bool
     {
         return $user->id === $this->id || $user->isAdmin();
     }
}
