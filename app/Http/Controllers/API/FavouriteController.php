<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodSpot;

class FavouriteController extends Controller
{
    // List the logged-in user's favourite food spots
    public function index(Request $request)
    {
        $user = $request->user();
        $favourites = $user->favouriteFoodSpots()->get();
        return response()->json($favourites);
    }

    // Add a food spot to the logged-in user's favourites
    public function store(Request $request, $foodSpotId)
    {
        $foodSpot = FoodSpot::findOrFail($foodSpotId);
        $user = $request->user();
        $user->favouriteFoodSpots()->syncWithoutDetaching([$foodSpot->id]);
        return response()->json(['message' => 'Added to favourites.']);
    }

    // Remove a food spot from the logged-in user's favourites
    public function destroy(Request $request, $foodSpotId)
    {
        $foodSpot = FoodSpot::findOrFail($foodSpotId);
        $user = $request->user();
        $user->favouriteFoodSpots()->detach($foodSpot->id);
        return response()->json(['message' => 'Removed from favourites.']);
    }
}
