<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FoodSpot;
use App\Http\Requests\StoreFoodSpotRequest;
use App\Http\Requests\UpdateFoodSpotRequest;

class FoodSpotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(FoodSpot::all());

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFoodSpotRequest $request)
    {
        $food_spot = FoodSpot::create($request->validated());
      
        return response()->json($food_spot, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $food_spot = FoodSpot::find($id);
        
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
        $food_spot->delete();
        
        return response()->json('OK', 200);
    }
}