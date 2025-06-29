<?php
use App\Http\Controllers\API\FoodSpotController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\FavouriteController;
use App\Http\Controllers\API\ReviewLikeController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/food-spots', [FoodSpotController::class, 'index']);
Route::get('/food-spots/{food_spot}/rating', [ReviewController::class, 'averageRating']);
Route::get('/images/{model_type}/{id}', [ImageController::class, 'viewAll']);
Route::get('/images/{model_type}/{id}/{image_id}', [ImageController::class, 'viewOne']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware(['throttle:6,1'])->name('verification.send');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User routes
    Route::apiResource('users', UserController::class);
    Route::get('/users/{user}/reviews', [UserController::class, 'reviews']); // Add this line
    Route::get('/users/{user}/food-spots', [FoodSpotController::class, 'userFoodSpots']);

    // Food spot routes
    Route::apiResource('food-spots', FoodSpotController::class)->except(['index']);
    Route::put('/food-spots/{id}/restore', [FoodSpotController::class, 'restore']);
    Route::delete('/food-spots/{id}/force', [FoodSpotController::class, 'forceDelete']);

    // Review routes
    Route::apiResource('food-spots.reviews', ReviewController::class);
    Route::put('/food-spots/{food_spot}/reviews/{review}/moderate', [ReviewController::class, 'moderate']);

    // Image routes
    Route::post('/images/{modelType}/{id}', [ImageController::class, 'upload']);
    Route::delete('/images/{model_type}/{id}/{image_id}', [ImageController::class, 'delete']);

    // Nominatim search endpoint
    Route::match(['get', 'post'], '/nominatim/search', [FoodSpotController::class, 'searchNominatim']);
    Route::post('/food-spots/from-nominatim', [FoodSpotController::class, 'createFromNominatim']);

    // Favourites routes
    Route::get('/favourites', [\App\Http\Controllers\API\FavouriteController::class, 'index']);
    Route::post('/food-spots/{foodSpotId}/favourite', [\App\Http\Controllers\API\FavouriteController::class, 'store']);
    Route::delete('/food-spots/{foodSpotId}/favourite', [\App\Http\Controllers\API\FavouriteController::class, 'destroy']);

    // Review likes routes
    Route::post('/reviews/{review}/like', [ReviewLikeController::class, 'toggle']);
    Route::get('/reviews/{review}/like', [ReviewLikeController::class, 'check']);
    Route::get('/reviews/{review}/likes/users', [ReviewLikeController::class, 'users']);
    Route::post('/reviews/likes/bulk-check', [ReviewLikeController::class, 'bulkCheck']);
});
