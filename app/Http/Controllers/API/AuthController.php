<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create($validated);

        return response()->json(['message' => 'User registered successfully'], 200);
    }

    public function login(LoginRequest $request)
    {
         // Retrieve the validated input data
        $credentials = $request->validated();

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
                
            // Get the authenticated user
            $user = Auth::user();

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['message' => 'Logged in successfully!', 'token' => $token], 200);
        }

        // Handle invalid credentials
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}