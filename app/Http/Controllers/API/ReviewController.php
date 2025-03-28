<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\FoodSpot;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Get reviews for a specific food spot.
     */
    public function index(FoodSpot $food_spot)
    {
        $reviews = $food_spot->reviews()
            ->where('is_approved', true)
            ->with('user:id,name')
            ->get();

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

        $review = Review::create([
            'food_spot_id' => $food_spot->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
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
        }

        $review->load('user:id,name');
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
