<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Dashboard\ChatController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\MedicalAdviceController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\AiDiagnosisController;
use Illuminate\Support\Facades\Route;

// ─── Auth Routes ──────────────────────────────────────────────────────────────

// Test AI Route
Route::view('/test-ai', 'test-ai');

Route::controller(SessionController::class)->group(function () {
    Route::get('/',       'login')->name('login');
    Route::get('/login',  'login')->name('login.create');
    Route::post('/login', 'authenticate')->name('login.authenticate');
});

Route::controller(GoogleAuthController::class)->prefix('auth/google')->as('google.')->group(function () {
    Route::get('/redirect', 'redirect')->name('redirect');
    Route::get('/callback', 'callback')->name('callback');
});

// ─── Protected Routes ─────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [SessionController::class, 'logout'])->name('logout');

    // Dashboard home
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Chat
    Route::prefix('chat')->controller(ChatController::class)->as('chat.')->group(function () {
        Route::get('/',                     'chat')->name('index');
        Route::get('/{userId}',             'chatConversation')->name('conversation');
        Route::post('/{userId}',            'sendMessage')->name('send');
        Route::get('/{userId}/messages',    'getNewMessages')->name('messages');
        Route::post('/{userId}/typing',     'typing')->name('typing');
    });

    // Users
    Route::prefix('users')->controller(UserController::class)->as('users.')->group(function () {
        Route::get('/',         'users')->name('index');
        Route::post('/',        'store')->name('store');
        Route::put('/{id}',     'update')->name('update');
        Route::delete('/{id}',  'delete')->name('delete');
    });

    // Medical Advice
    Route::prefix('medical-advice')->controller(MedicalAdviceController::class)->as('medical-advice.')->group(function () {
        Route::get('/',         'index')->name('index');
        Route::post('/',        'store')->name('store');
        Route::put('/{id}',     'update')->name('update');
        Route::delete('/{id}',  'destroy')->name('destroy');
    });

    // AI Diagnoses
    Route::prefix('ai-diagnoses')->controller(AiDiagnosisController::class)->as('ai-diagnoses.')->group(function () {
        Route::get('/',         'index')->name('index');
        Route::delete('/{id}',  'destroy')->name('destroy');
    });

    // Settings
    Route::prefix('settings')->controller(SettingsController::class)->as('settings.')->group(function () {
        Route::get('/',  'index')->name('index');
        Route::post('/', 'update')->name('update');
    });

    // Language Switcher
    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['ar', 'en'])) {
            session(['locale' => $locale]);
        }
        return back();
    })->name('lang.switch');

    // Missing Modules (Prepared Routes)
    Route::prefix('assessments')->controller(\App\Http\Controllers\Dashboard\AssessmentController::class)->as('assessments.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{assessment}', 'show')->name('show');
        Route::post('/{assessment}/review', 'review')->name('review');
    });

    Route::prefix('contact-messages')->controller(\App\Http\Controllers\Dashboard\ContactUsController::class)->as('contact-messages.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/{id}/read', 'markRead')->name('read');
    });
});