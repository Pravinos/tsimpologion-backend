<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\FoodSpot;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'food_spot_id',
        'user_id',
        'comment',
        'rating',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function foodSpot(): BelongsTo
    {
        return $this->belongsTo(FoodSpot::class);
    }
}