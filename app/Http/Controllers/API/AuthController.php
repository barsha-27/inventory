<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create a Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response
        return response()->json([
            'status' => 1,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user
        ]);
    }
    public function login(Request $request)
{
    // Validate input
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    // Check user credentials
    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => 0,
            'message' => 'Invalid credentials'
        ], 401);
    }

    // Create Sanctum token
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status' => 1,
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user
    ]);
}

}
