<?php
namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = Review::all();
        $users = User::where('role', '!=', 'admin')->get();

        foreach ($reviews as $review) {
            // Each review gets a random number of likes (0-5)
            $likeCount = rand(0, 5);

            // Skip if no likes
            if ($likeCount === 0) {
                continue;
            }

            // Select random users to like this review
            $likingUsers = $users->random($likeCount);

            foreach ($likingUsers as $user) {
                // Don't let users like their own reviews
                if ($user->id !== $review->user_id) {
                    ReviewLike::create([
                        'user_id' => $user->id,
                        'review_id' => $review->id,
                    ]);
                }
            }
        }
    }
}
