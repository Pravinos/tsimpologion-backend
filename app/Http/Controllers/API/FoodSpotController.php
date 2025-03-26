<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FoodSpot;
use App\Http\Requests\StoreFoodSpotRequest;
use App\Http\Requests\UpdateFoodSpotRequest;
use Illuminate\Http\Request;

class FoodSpotController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(FoodSpot::class, 'food_spot');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(FoodSpot::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFoodSpotRequest $request)
    {
        $validated = $request->validated();
        
        // Assign ownership to the current user if they're a spot owner
        if (auth()->user()->isSpotOwner()) {
            $validated['owner_id'] = auth()->id();
        }
        
        $food_spot = FoodSpot::create($validated);
      
        return response()->json($food_spot, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FoodSpot $food_spot)
    {
        return response()->json($food_spot, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFoodSpotRequest $request, FoodSpot $food_spot)
    {
        $food_spot->update($request->validated());

        return response()->json($food_spot, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodSpot $food_spot)
    {
        $food_spot->delete(); // Soft delete
        
        return response()->json(['message' => 'Food spot deleted successfully'], 200);
    }

    /**
     * Restore a soft-deleted food spot (admin only).
     */
    public function restore($id)
    {
        $food_spot = FoodSpot::withTrashed()->findOrFail($id);
        $this->authorize('restore', $food_spot);
        
        $food_spot->restore();
        
        return response()->json($food_spot, 200);
    }

    /**
     * Permanently delete a food spot (admin only).
     */
    public function forceDelete($id)
    {
        $food_spot = FoodSpot::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $food_spot);
        
        $food_spot->forceDelete();
        
        return response()->json(['message' => 'Food spot permanently deleted'], 200);
    }
}