<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google_Client;
use App\Models\User;
use App\Traits\ApiTrait;

class GoogleAuthController extends Controller
{
    use ApiTrait;
    public function googleAuth(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return $this->unauthorizedResponse([], 'Invalid Google token');
            }

            $email = $payload['email'];
            $name = $payload['name'] ?? 'No Name';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt(str()->random(16)),
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->okResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Login successful');

        } catch (\Exception $e) {
            return $this->errorResponse(['message' => $e->getMessage()], 500, 'Authentication failed');
        }
    }
}
