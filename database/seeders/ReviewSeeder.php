<?php
namespace Database\Seeders;

use App\Models\FoodSpot;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $food_spots = FoodSpot::all();
        $users = User::where('role', '!=', 'admin')->get();

        foreach ($food_spots as $food_spot) {
            // Add 3-5 reviews per food spot
            $reviewCount = rand(3, 5);
            $selectedUsers = $users->random($reviewCount);

            foreach ($selectedUsers as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'food_spot_id' => $food_spot->id,
                    'rating' => rand(3, 5), // Mostly positive reviews
                    'comment' => 'This is a sample review for ' . $food_spot->name . '. The food was great and the atmosphere was nice.',
                    'is_approved' => true,
                ]);
            }

            // Update the food spot rating
            $food_spot->rating = $food_spot->getAverageRatingAttribute();
            $food_spot->save();
        }
    }
}