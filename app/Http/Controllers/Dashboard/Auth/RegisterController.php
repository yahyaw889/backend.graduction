<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    //create
    public function create()
    {
        return view('auth.registrer');
    }

    //store
    public function store(RegisterRequest $request)
    {
        //validation
        $validated = $request->validated();
        //create user
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        //login user
        Auth::login($user);
        //redirect
        return redirect()->route('home'); //you can change it later
    }
}
