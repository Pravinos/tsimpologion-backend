<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewLike extends Model
{
    protected $fillable = [
        'user_id',
        'review_id',
    ];

    /**
     * Get the user who liked the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the review that was liked.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
