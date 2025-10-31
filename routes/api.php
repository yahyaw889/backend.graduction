<?php

use App\Http\Controllers\Api\Auth\AuthSessionController;
use App\Http\Controllers\Api\Chat\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\ContactUsController;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [AuthSessionController::class, 'login']);
// Route::post('/auth/google/callback', [GoogleAuthController::class, 'googleLogin'])->name('google.login'); //need more info

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user', [AuthSessionController::class, 'user']);
    Route::post('/logout', [AuthSessionController::class, 'logout']);



    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/messages/{userId}', [ChatController::class, 'getMessages']);


    Route::post('/contact-us', [ContactUsController::class, 'store']);
});


