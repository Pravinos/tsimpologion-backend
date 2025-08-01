<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Traits\HasImages;

class FoodSpot extends Model
{
    use HasFactory, HasImages;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'city',
        'address',
        'description',
        'info_link',
        'rating',
        'owner_id',
        'images',
        'phone',
        'business_hours',
        'social_links',
        'price_range',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'float',
        'images' => 'array',
        'business_hours' => 'array',
        'social_links' => 'array',
    ];

     /**
     * Get the user that owns the food spot.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the reviews for the food spot.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the average rating of the food spot.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    /**
     * The users who have favourited this food spot.
     */
    public function favouritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favourites')->withTimestamps();
    }

    // If needed, override the method
    public function getImageFolder(): string
    {
        return 'food-spots';
    }
}
