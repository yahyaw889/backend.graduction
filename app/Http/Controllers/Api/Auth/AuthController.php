<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Google_Client;
use App\Helpers\ApiResponse;
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
            return ApiResponse::error("Validation error", $validator->errors(), 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                // 'phone' => $request->phone,
                'password' => Hash::make($request->password),

            ]);

            $token = $user->createToken("api-token")->plainTextToken;

            return ApiResponse::success([
                'token' => $token,
                'user' => $this->userData($user)
            ], 'User registered successfully', 201);

        } catch (\Throwable $th) {
            Log::channel("api")->error($th);
            return ApiResponse::error("Registration failed", collect(['message' => [$th->getMessage()]]), 500);
        }
    }

    // ***************** register with google or facebook **************
    public function googleRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error("Validation error", $validator->errors(), 422);
        }

        try {
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return ApiResponse::error('Invalid Google token', collect(['id_token' => ['رمز Google غير صالح']]), 401);
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

            return ApiResponse::success([
                'token' => $token,
                'user' => $this->userData($user)
            ], 'Login with Google successful', 200);

        } catch (\Exception $e) {
            Log::channel("api")->error($e);
            return ApiResponse::error("Google authentication failed", collect(['message' => [$e->getMessage()]]), 500);
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
            return ApiResponse::error("Validation error", $validator->errors(), 422);
        }
        try {

            // if (!Auth::attempt($request->only('email', 'password'))) {
            //     return ApiResponse::error("Invalid credentials", collect(['email' => ['Invalid email or password.']]), 401);
            // }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return ApiResponse::error("Invalid email", collect([
                    'email' => ['الايميل غير موجود']
                ]), 401);
            }
            if (!Hash::check($request->password, $user->password)) {
                return ApiResponse::error("Invalid password", collect([
                    'password' => ['خطأ في كلمة المرور']
                ]), 401);
            }
            $token = $user->createToken("api-token")->plainTextToken;
            return ApiResponse::success([
                'token' => $token,
                'user' => $this->userData($user)
            ], 'Login successful');

        } catch (\Throwable $th) {
            Log::channel("api")->error($th);
            return ApiResponse::error("Unexpected error", collect(['message' => [$th->getMessage()]]), 500);
        }
    }

    // ************************ logout api *****************************

    public function logout(Request $request)
    {
        try {
            // dd($request->user);
            $request->user()->tokens()->delete();                  //delete from all browser
            // $request->user()->currentAccessToken()->delete();   //delete form one browser
            return ApiResponse::success([], "logged out successfully ");
        } catch (\Throwable $th) {
            Log::channel("Posts")->error($th->getMessage() . $th->getFile() . $th->getLine());
            return ApiResponse::error("login failed  ", [], 500);
        }
    }


    // ****************** get user data by id ***************

    public function getUserData($id)
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::error('User not found ', [], 404);
        }
        return ApiResponse::success(
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
