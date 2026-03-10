<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;

// --- Public Routes ---
Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// --- Protected Routes (Require Login) ---
Route::middleware('auth')->group(function () {
    
    // Page Views
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Page View for the Announcement Board
    Route::get('/announcements-board', function () {
        return view('announcement-board'); // This must match the filename exactly
    })->name('announcements.index');
    // --- API Routes for Vue ---
    Route::prefix('api')->group(function () {
        // Fetch all announcements (calls your index method)
        Route::get('/announcements', [AnnouncementController::class, 'index']);
        
        // Save new announcement (calls your store method)
        Route::post('/announcements', [AnnouncementController::class, 'store']);
        
        // Delete announcement (calls your destroy method)
        // Using {id} allows your destroy($id) function to receive the correct parameter
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});