<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class User extends Controller
{
    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            // Authentication passed
            $user = auth()->user();
            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);
        } else {
            // Authentication failed
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }
    }
}
