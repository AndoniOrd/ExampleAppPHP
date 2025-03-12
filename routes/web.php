<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CampaignPlanningController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MailingListController;
use App\Http\Controllers\CampaignReportController;
use App\Http\Controllers\EmailContactController; // Add this import
use App\Http\Controllers\EmailContactFileController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard/{id}', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Existing resources
Route::resource('campaign-plannings', CampaignPlanningController::class);
Route::resource('campaign-reports', CampaignReportController::class);

// Add the new EmailContact resource
Route::resource('email-contacts', EmailContactController::class);

Route::get('/import-contacts', [EmailContactFileController::class, 'showImportForm'])->name('contacts.import.form');
Route::post('/import-contacts', [EmailContactFileController::class, 'import'])->name('contacts.import');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';