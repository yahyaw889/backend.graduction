<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    use ApiTrait;
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
        return $this->createdResponse([
            'user'    => $user,
            'token'   => $token,
        ], 'User registered successfully');
    }
}
