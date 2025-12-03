<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::controller(SessionController::class)->group(function (){
    Route::get('/', 'login')->name('login');
    Route::get('/login', 'login')->name('login.create');
    Route::post('/login', 'authenticate')->name('login.authenticate');
});

Route::controller(GoogleAuthController::class)->prefix('auth/google')->as('google.')->group(function () {
    Route::get('/redirect', 'redirect')->name('redirect');
    Route::get('/callback', 'callback')->name('callback');
});



Route::middleware('auth')->group(function () {

    Route::controller(SessionController::class)->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });



    Route::prefix('chat')->controller(ChatController::class)->as('chat.')->group(function(){
        Route::get('/', 'chat')->name('index');
        Route::get('/{userId}', 'chatConversation')->name('conversation');
        Route::post('/{userId}', 'sendMessage')->name('send');
    });

    Route::prefix('reports')->controller(ReportController::class)->as('reports.')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'delete')->name('delete');
    });

    

    Route::prefix('users')->controller(UserController::class)->as('users.')->group(function(){
        Route::get('/', 'users')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'delete')->name('delete');
    });

    Route::get('/home', [DashboardController::class, 'index'])->name('home');

});




    
    // Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
    //     Route::get('/', 'index')->name('dashboard');
    //     Route::get('/reports', 'reports')->name('reports');
    //     Route::post('/reports', 'storeReport')->name('reports.store');
    //     Route::put('/reports/{id}', 'updateReport')->name('reports.update');
    //     Route::delete('/reports/{id}', 'deleteReport')->name('reports.delete');
    //     Route::get('/chat', 'chat')->name('chat');
    //     Route::get('/chat/{userId}', 'chatConversation')->name('chat.conversation');
    //     Route::post('/chat/{userId}', 'sendMessage')->name('chat.send');
    // });