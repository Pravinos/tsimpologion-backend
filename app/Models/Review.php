<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\FoodSpot;
use App\Traits\HasImages;

class Review extends Model
{
    use HasFactory, HasImages, SoftDeletes;

    protected $fillable = [
        'food_spot_id',
        'user_id',
        'comment',
        'rating',
        'is_approved',
        'images',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'images' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function foodSpot(): BelongsTo
    {
        return $this->belongsTo(FoodSpot::class);
    }

     // Only review authors can manage review images
     public function userCanManageImages($user): bool
     {
         return $user->id === $this->user_id || $user->isAdmin();
     }

     /**
      * Get the likes for this review.
      */
     public function likes(): HasMany
     {
         return $this->hasMany(ReviewLike::class);
     }

     /**
      * Check if a user has liked this review.
      */
     public function isLikedByUser($userId): bool
     {
         return $this->likes()->where('user_id', $userId)->exists();
     }

     /**
      * Get the like count for this review.
      */
     public function getLikeCountAttribute(): int
     {
         return $this->likes()->count();
     }
}
