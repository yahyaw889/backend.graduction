<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    // Register API
    public function register(RegisterRequest $request)
    {
        // Validate input
        $validation = $request->validated();

        DB::beginTransaction();
        // Create user
        $user = User::create([
            'name'     => $validation['name'],
            'email'    => $validation['email'],
            'password' => Hash::make($validation['password']),
        ]);

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        DB::commit();
        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }
}
