<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');

//guest
Route::middleware('guest')->group(function () {
    //register
    Route::controller(RegisterController::class)->group(function () {
        //create
        Route::get('/register', 'create')->name('register.create');
        //store
        Route::post('/register', 'store')->name('register.store');
    });

    //session
    Route::controller(SessionController::class)->group(function () {
        //login
        Route::get('/login', 'login')->name('login.create');
        //authenticate
        Route::post('/login', 'authenticate')->name('login.authenticate');
    });

    //google
    Route::controller(GoogleAuthController::class)->group(function () {
        //redirect
        Route::get('/auth/google/redirect', 'redirect')->name('google.redirect');
        //callback
        Route::get('/auth/google/callback', 'callback')->name('google.callback');
    });

    
});

//auth
Route::middleware('auth')->group(function () {

    Route::controller(SessionController::class)->group(function () {
        //logout
        Route::post('/logout', 'logout')->name('logout');
    });
});


Route::get('/home', function () {
    return view('home');
})->middleware('auth')->name('home');
