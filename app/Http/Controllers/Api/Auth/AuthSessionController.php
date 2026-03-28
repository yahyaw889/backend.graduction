<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Hash;

class AuthSessionController extends Controller
{
    use ApiTrait;
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',    
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->unauthorizedResponse([], 'Invalid credentials');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->okResponse([
            'user'    => $user,
            'token'   => $token,
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->okResponse([], 'Logged out successfully');
    }

    public function user(Request $request)
    {
        return $this->okResponse(
            $request->user(),
            'User data retrieved successfully'
        );
    }
}
