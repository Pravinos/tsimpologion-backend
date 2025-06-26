<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Services\ImageService;
use App\Models\FoodSpot;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->imageService = $imageService;
    }

    /**
     * Get reviews for a specific food spot.
     */
    public function index(Request $request, FoodSpot $food_spot)
    {
        $query = $food_spot->reviews()
            ->where('is_approved', true)
            ->with('user:id,username')
            ->withCount('likes');

        // Apply sorting based on request
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'most_liked':
                    $query->orderBy('likes_count', 'desc');
                    break;
                case 'recent':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // Default sorting by most recent
            $query->orderBy('created_at', 'desc');
        }

        $reviews = $query->get();

        // Append is_liked field if user is authenticated
        if (auth()->check()) {
            $userId = auth()->id();
            
            // Get all review IDs to check in a single query
            $reviewIds = $reviews->pluck('id')->toArray();
            
            // Get all likes for these reviews by this user in a single query
            $userLikes = \App\Models\ReviewLike::where('user_id', $userId)
                ->whereIn('review_id', $reviewIds)
                ->pluck('review_id')
                ->toArray();
            
            // Mark reviews as liked based on the bulk query results
            $reviews->each(function ($review) use ($userLikes) {
                $review->is_liked = in_array($review->id, $userLikes);
            });
        }

        return response()->json($reviews, 200);
    }

    /**
     * Create a new review for a food spot.
     */
    public function store(StoreReviewRequest $request, FoodSpot $food_spot)
    {
        // Check if user already reviewed this food spot
        $existingReview = Review::where('user_id', auth()->id())
            ->where('food_spot_id', $food_spot->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this food spot',
            ], 422);
        }

        $validated = $request->validated();

        $review = Review::create([
            'food_spot_id' => $food_spot->id,
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // Update the food spot's average rating
        $food_spot->rating = $food_spot->getAverageRatingAttribute();
        $food_spot->save();

        return response()->json($review, 201);
    }

    /**
     * Show a specific review.
     */
    public function show(FoodSpot $food_spot, Review $review)
    {
        // Verify the review belongs to this food spot
        if ($review->food_spot_id !== $food_spot->id) {
            return response()->json(['message' => 'Review not found for this food spot'], 404);
        }        $review->load('user:id,username');
        $review->loadCount('likes');
        
        // Add is_liked field if user is authenticated
        if (auth()->check()) {
            $userId = auth()->id();
            $review->is_liked = \App\Models\ReviewLike::where('user_id', $userId)
                ->where('review_id', $review->id)
                ->exists();
        }

        return response()->json($review, 200);
    }

    /**
     * Update a review.
     */
    public function update(UpdateReviewRequest $request, FoodSpot $food_spot, Review $review)
    {
        // Verify the review belongs to this food spot
        if ($review->food_spot_id !== $food_spot->id) {
            return response()->json(['message' => 'Review not found for this food spot'], 404);
        }

        // Authorize the action
        $this->authorize('update', $review);

        $review->update($request->validated());

        // Update the food spot's average rating
        $food_spot->rating = $food_spot->getAverageRatingAttribute();
        $food_spot->save();

        return response()->json($review, 200);
    }

    /**
     * Delete a review.
     */
    public function destroy(FoodSpot $food_spot, Review $review)
    {
        // Verify the review belongs to this food spot
        if ($review->food_spot_id !== $food_spot->id) {
            return response()->json(['message' => 'Review not found for this food spot'], 404);
        }

        // Authorize the action
        $this->authorize('delete', $review);

        // Delete all images associated with the review
        if (!empty($review->images) && is_array($review->images)) {
            foreach ($review->images as $image) {
                if (isset($image['id'])) {
                    $this->imageService->deleteImage($review, $image['id']);
                }
            }
        }

        $review->delete();

        // Update the food spot's average rating after deleting
        $food_spot->rating = $food_spot->getAverageRatingAttribute();
        $food_spot->save();

        return response()->json(['message' => 'OK', 200]);
    }

    /**
     * Moderate a review (admin only).
     */
    public function moderate(Request $request, FoodSpot $food_spot, Review $review)
    {
        // Verify the review belongs to this food spot
        if ($review->food_spot_id !== $food_spot->id) {
            return response()->json(['message' => 'Review not found for this food spot'], 404);
        }

        $this->authorize('moderate', $review);

        $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $review->is_approved = $request->is_approved;
        $review->save();

        // Update the food spot's average rating
        $food_spot->rating = $food_spot->getAverageRatingAttribute();
        $food_spot->save();

        return response()->json($review, 200);
    }

    /**
     * Get the average rating for a food spot.
     */
    public function averageRating(FoodSpot $food_spot)
    {
        return response()->json([
            'message' => 'Average rating retrieved successfully',
            'data' => [
                'food_spot_id' => $food_spot->id,
                'name' => $food_spot->name,
                'average_rating' => $food_spot->getAverageRatingAttribute(),
                'review_count' => $food_spot->reviews()->where('is_approved', true)->count(),
            ]
        ], 200);
    }
}
