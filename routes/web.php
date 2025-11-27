<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SessionController;



//guest
Route::middleware('guest')->group(function () {

    Route::controller(SessionController::class)->group(function () {
        Route::get('/', 'login')->name('login.create');
        Route::post('/login', 'authenticate')->name('login.authenticate');
    });
    Route::controller(GoogleAuthController::class)->group(function () {
        Route::get('/auth/google/redirect', 'redirect')->name('google.redirect');
        Route::get('/auth/google/callback', 'callback')->name('google.callback');
    });
});

Route::middleware('auth')->group(function () {

    Route::controller(SessionController::class)->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });
});


Route::get('/home', function () {
    return view('home');
})->middleware('auth')->name('home');
