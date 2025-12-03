<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Google\Client as Google_Client;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

// ***************************  this page is under maintenance ***********************************
class AuthController extends Controller
{
    use ApiTrait;
    // use Images;

    // ******************* Register *************************

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            // 'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->unprocessableResponse($validator->errors(), 'Validation error');
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                // 'phone' => $request->phone,
                'password' => Hash::make($request->password),

            ]);

            $token = $user->createToken("api-token")->plainTextToken;

            return $this->createdResponse([
                'token' => $token,
                'user' => $this->userData($user)
            ], 'User registered successfully');

        } catch (\Throwable $th) {
            Log::channel("api")->error($th);
            return $this->errorResponse(['message' => [$th->getMessage()]], 500, 'Registration failed');
        }
    }

    // ***************** register with google or facebook **************
    public function googleRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->unprocessableResponse($validator->errors(), 'Validation error');
        }

        try {
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return $this->unauthorizedResponse(['id_token' => ['رمز Google غير صالح']], 'Invalid Google token');
            }

            $email = $payload['email'];
            $name = $payload['name'] ?? 'No Name';
            $google_id = $payload['sub'];

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'google_id' => $google_id,
                    'password' => Hash::make(str()->random(16)),
                ]
            );

            $token = $user->createToken('api-token')->plainTextToken;

            return $this->okResponse([
                'token' => $token,
                'user' => $this->userData($user)
            ], 'Login with Google successful');

        } catch (\Exception $e) {
            Log::channel("api")->error($e);
            return $this->errorResponse(['message' => [$e->getMessage()]], 500, 'Google authentication failed');
        }
    }

    // ************************ Login ****************************
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->unprocessableResponse($validator->errors(), 'Validation error');
        }
        try {

            // if (!Auth::attempt($request->only('email', 'password'))) {
            //     return ApiResponse::error("Invalid credentials", collect(['email' => ['Invalid email or password.']]), 401);
            // }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->unauthorizedResponse([
                    'email' => ['الايميل غير موجود']
                ], 'Invalid email');
            }
            if (!Hash::check($request->password, $user->password)) {
                return $this->unauthorizedResponse([
                    'password' => ['خطأ في كلمة المرور']
                ], 'Invalid password');
            }
            $token = $user->createToken("api-token")->plainTextToken;
            return $this->okResponse([
                'token' => $token,
                'user' => $this->userData($user)
            ], 'Login successful');

        } catch (\Throwable $th) {
            Log::channel("api")->error($th);
            return $this->errorResponse(['message' => [$th->getMessage()]], 500, 'Unexpected error');
        }
    }

    // ************************ logout api *****************************

    public function logout(Request $request)
    {
        try {
            // dd($request->user);
            $request->user()->tokens()->delete();                  //delete from all browser
            // $request->user()->currentAccessToken()->delete();   //delete form one browser
            return $this->okResponse([], 'Logged out successfully');
        } catch (\Throwable $th) {
            Log::channel("Posts")->error($th->getMessage() . $th->getFile() . $th->getLine());
            return $this->errorResponse([], 500, 'Logout failed');
        }
    }


    // ****************** get user data by id ***************

    public function getUserData($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->notFoundResponse([], 'User not found');
        }
        return $this->okResponse(
            $this->userData($user),
            'User data retrieved successfully'
        );
    }


    // ************************ Helper Function ****************************
    private function userData($user)
    {

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            // 'phone' => $user->phone,
        ];
    }

}
