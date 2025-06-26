<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewLike;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewLikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Toggle like for a review.
     *
     * @param Review $review The review to like/unlike
     * @return JsonResponse
     */
    public function toggle(Review $review): JsonResponse
    {
        $userId = auth()->id();
        $existingLike = ReviewLike::where('review_id', $review->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            // Unlike the review
            $existingLike->delete();
            $action = 'unliked';
        } else {
            // Like the review
            ReviewLike::create([
                'review_id' => $review->id,
                'user_id' => $userId
            ]);
            $action = 'liked';
        }

        return response()->json([
            'message' => "Review {$action} successfully",
            'data' => [
                'review_id' => $review->id,
                'like_count' => $review->getLikeCountAttribute(),
                'is_liked' => $action === 'liked'
            ]
        ], 200);
    }

    /**
     * Check if user has liked a review.
     *
     * @param Review $review The review to check
     * @return JsonResponse
     */
    public function check(Review $review): JsonResponse
    {
        $userId = auth()->id();
        $isLiked = $review->isLikedByUser($userId);

        return response()->json([
            'message' => 'Like status retrieved successfully',
            'data' => [
                'review_id' => $review->id,
                'is_liked' => $isLiked,
                'like_count' => $review->getLikeCountAttribute()
            ]
        ], 200);
    }

    /**
     * Get users who liked a review.
     *
     * @param Review $review The review to get likes for
     * @return JsonResponse
     */
    public function users(Review $review): JsonResponse
    {
        $users = $review->likes()
            ->with('user:id,username,first_name,last_name')
            ->get()
            ->pluck('user');

        return response()->json([
            'message' => 'Users who liked this review retrieved successfully',
            'data' => $users
        ], 200);
    }

    /**
     * Bulk check if user has liked multiple reviews.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkCheck(Request $request): JsonResponse
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'integer|exists:reviews,id'
        ]);

        $userId = auth()->id();
        $reviewIds = $request->review_ids;

        // Get all likes for these reviews by this user in a single query
        $userLikes = ReviewLike::where('user_id', $userId)
            ->whereIn('review_id', $reviewIds)
            ->pluck('review_id')
            ->toArray();

        // Get like counts for these reviews
        $likeCounts = Review::whereIn('id', $reviewIds)
            ->withCount('likes')
            ->get()
            ->pluck('likes_count', 'id')
            ->toArray();

        $result = [];
        foreach ($reviewIds as $reviewId) {
            $result[$reviewId] = [
                'is_liked' => in_array($reviewId, $userLikes),
                'like_count' => $likeCounts[$reviewId] ?? 0
            ];
        }

        return response()->json([
            'message' => 'Like status retrieved successfully',
            'data' => $result
        ], 200);
    }
}
