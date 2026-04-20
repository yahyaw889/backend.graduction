<?php

use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\Auth\AuthSessionController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\SymptomController;
use App\Http\Controllers\Api\AiDiagnosisController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthSessionController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/auth/google', [GoogleAuthController::class, 'googleAuth']);

// AI Diagnosis Routes (Public for testing)
Route::post('/ai-diagnosis', [AiDiagnosisController::class, 'diagnose']);
Route::get('/ai-diagnosis/image/{filename}', [AiDiagnosisController::class, 'showImage']);
Route::get('/ai-usage', [AiDiagnosisController::class, 'usageStats']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthSessionController::class, 'logout']);
    Route::post('/user', [AuthSessionController::class, 'user']);

    Route::get('/' , [HomeController::class  , 'index']);
    Route::get('symptoms' , [SymptomController::class , 'index']);
    Route::controller(ReminderController::class)->prefix('reminders')->group(function (){
            Route::get('/',  'index');
            Route::post('/', 'store');
            Route::get('upcoming/list', 'upcoming');
            Route::get('{reminder}', 'show');
            Route::put('{reminder}', 'update');
            Route::delete('{reminder}', 'destroy');
            Route::post('{reminder}/toggle', 'toggle');
            Route::get('{reminder}/next-occurrences', 'nextOccurrences');
            Route::post('{reminder}/exceptions', 'addException');
            Route::delete('exceptions/{exception}', 'deleteException');
    });



    // AI Diagnosis History (Protected)
    Route::get('/ai-diagnosis', [AiDiagnosisController::class, 'index']);

    // Assessments (Medical History)
    
    Route::controller(AssessmentController::class)->prefix('assessments')->group(function (){
        Route::get('stats/statistics', 'statistics');
        Route::get('{assessment}', 'show');
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::delete('{assessment}', 'destroy');
    });
  
    // Chat
    Route::prefix('chat')->controller(ChatController::class)->group(function () {
        Route::get('/conversations', 'conversations');
        Route::get('/doctors', 'doctors');
        Route::get('/{userId}', 'show');
        Route::post('/send', 'send');
        Route::patch('/{messageId}/read', 'markAsRead');
        Route::post('/typing', 'typing');
    });

    
    // Contact Us
    Route::post('/contact-us', [ContactUsController::class, 'store']);
});
