<?php
use App\Http\Controllers\API\FoodSpotController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/food-spots', [FoodSpotController::class, 'index']);
Route::get('/reviews/{review}', [ReviewController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User routes
    Route::apiResource('users', UserController::class);

    // Food spot routes
    Route::apiResource('food-spots', FoodSpotController::class)->except(['index']);
    Route::put('/food-spots/{id}/restore', [FoodSpotController::class, 'restore']);
    Route::delete('/food-spots/{id}/force', [FoodSpotController::class, 'forceDelete']);

    // Review routes
     Route::post('/food-spots/{foodSpot}/reviews', [ReviewController::class, 'store']);
     Route::put('/reviews/{review}', [ReviewController::class, 'update']);
     Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // Admin-only route
    Route::put('/reviews/{review}/moderate', [ReviewController::class, 'moderate']);
});