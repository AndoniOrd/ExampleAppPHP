<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CampaignPlanningController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MailingListController;  // Ensure MailingListController is imported

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard/{id}', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::resource('campaign-plannings', CampaignPlanningController::class);


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
