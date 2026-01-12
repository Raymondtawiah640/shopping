<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ShoppingUser
{
    public function login($credentials)
    {
        Log::info('Login attempt in service', ['email' => $credentials['email']]);
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Log::info('Login successful in service', ['user' => $user]);
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
            ]);
        }
        
        Log::info('Login failed in service: Invalid credentials');
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
}