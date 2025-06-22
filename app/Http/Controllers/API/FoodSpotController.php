<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FoodSpot;
use App\Http\Requests\StoreFoodSpotRequest;
use App\Http\Requests\UpdateFoodSpotRequest;
use Illuminate\Http\Request;
use App\Services\NominatimService;
use App\Models\User;

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
     * Display a listing of the resource for a given user.
     */
    public function userFoodSpots(User $user)
    {
        $foodSpots = FoodSpot::where('owner_id', $user->id)->get();
        return response()->json($foodSpots, 200);
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

        return response()->json(['message' => 'OK'], 200);
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

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Search for food spots using Nominatim API.
     */
    public function searchNominatim(Request $request, NominatimService $nominatimService)
    {
        $request->validate([
            'q' => 'required|string',
        ]);
        $results = $nominatimService->search($request->input('q'));
        return response()->json($results);
    }

    /**
     * Create a food spot from Nominatim API response.
     */
    public function createFromNominatim(Request $request, NominatimService $nominatimService)
    {
        $validated = $request->validate([
            'q' => 'required|string',
        ]);
        $results = $nominatimService->search($validated['q']);
        if (empty($results) || !isset($results[0])) {
            return response()->json(['error' => 'No results found from Nominatim.'], 404);
        }
        $nominatim = $results[0];
        // Compose address
        $addressParts = [];
        if (!empty($nominatim['address']['road'] ?? null)) {
            $addressParts[] = $nominatim['address']['road'];
        }
        if (!empty($nominatim['address']['house_number'] ?? null)) {
            $addressParts[] = $nominatim['address']['house_number'];
        }
        if (!empty($nominatim['address']['postcode'] ?? null)) {
            $addressParts[] = $nominatim['address']['postcode'];
        }
        $address = implode(', ', $addressParts);
        $info_link = sprintf(
            'https://www.openstreetmap.org/?mlat=%s&mlon=%s#map=19/%s/%s',
            $nominatim['lat'],
            $nominatim['lon'],
            $nominatim['lat'],
            $nominatim['lon']
        );

        // Compose city with normalization and fallback
        $city = $nominatim['address']['city']
            ?? $nominatim['address']['town']
            ?? $nominatim['address']['village']
            ?? $nominatim['address']['municipality']
            ?? $nominatim['address']['state']
            ?? null;

        $food_spot = FoodSpot::create([
            'name' => $nominatim['name'] ?? ($nominatim['display_name'] ?? $validated['q']),
            'category' => ucfirst(str_replace('_', ' ', $nominatim['type'] ?? 'Restaurant')),
            'city' => $city,
            'address' => $address,
            'description' => null,
            'info_link' => $info_link,
            'rating' => null,
            'owner_id' => null,
            'images' => null,
            'phone' => null,
            'business_hours' => null,
            'social_links' => null,
            'price_range' => null,
        ]);

        return response()->json($food_spot, 201);
    }
}
