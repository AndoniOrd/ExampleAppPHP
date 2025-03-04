<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MailingListController;  // Ensure MailingListController is imported

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Define the mailinglists routes
Route::prefix('api')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('mailinglists', MailingListController::class);  // Add mailinglists route
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
