<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FacebookAuthController extends Controller
{
    public function redirect()
    {
        dd('redirect work');
        // return Socialite::driver('facebook')->redirect();
    }

    public function callback()
    {
        dd('callback work');
        // $googleUser = Socialite::driver('facebook')->user();

        // $user = User::firstOrCreate(
        //     [
        //         'email' => $googleUser->getEmail(),
        //     ],
        //     [
        //         'name' => $googleUser->getName(),
        //         'email_verified_at' => now(),
        //     ]
        // );

        // Auth::login($user);

        // return redirect('/home');
    }
}
