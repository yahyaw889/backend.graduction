<?php

use App\Http\Controllers\Api\Auth\AuthSessionController;
use App\Http\Controllers\Api\Chat\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\ContactUsController;

Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [AuthSessionController::class, 'login'])->name('login');
Route::post('/auth/google', [GoogleAuthController::class, 'googleAuth'])->name('google.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user', [AuthSessionController::class, 'user']);
    Route::post('/logout', [AuthSessionController::class, 'logout']);



    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/messages/{userId}', [ChatController::class, 'getMessages']);


    Route::post('/contact-us', [ContactUsController::class, 'store']);
});


