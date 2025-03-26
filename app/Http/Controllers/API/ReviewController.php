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
        $this->authorizeResource(Review::class, 'review');
    }

    /**
     * Get reviews for a specific food spot.
     */
    public function index(FoodSpot $food_spot)
    {
        return response()->json(
            $food_spot->reviews()
                ->with('user:id,name') // Include user name but not email
                ->where('is_approved', true)
                ->get()
        );
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
    public function show(Review $review)
    {
        $review->load('user:id,name');
        return response()->json($review);
    }

    /**
     * Update a review.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $review->update($request->validated());

        // Update the food spot's average rating
        $food_spot = $review->foodSpot;
        $food_spot->rating = $food_spot->getAverageRatingAttribute();
        $food_spot->save();

        return response()->json($review);
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review)
    {
        $food_spot = $review->foodSpot;
        $review->delete();

        // Update the food spot's average rating after deleting
        $food_spot->rating = $food_spot->getAverageRatingAttribute();
        $food_spot->save();

        return response()->json(['message' => 'Review deleted successfully']);
    }

    /**
     * Moderate a review (admin only).
     */
    public function moderate(Request $request, Review $review)
    {
        $this->authorize('moderate', $review);

        $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $review->is_approved = $request->is_approved;
        $review->save();

        // Update the food spot's average rating
        $food_spot = $review->foodSpot;
        $food_spot->rating = $food_spot->getAverageRatingAttribute();
        $food_spot->save();

        return response()->json($review);
    }
}
