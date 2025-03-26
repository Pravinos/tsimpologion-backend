<?php

use App\Http\Controllers\API\FoodSpotController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Food Spot API Routes
Route::apiResource('/food-spots', FoodSpotController::class);
Route::apiResource('/users', UserController::class);