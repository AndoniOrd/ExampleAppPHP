<?php

use App\Mail\TestEmail;
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

// routes/web.php
Route::get('/enviar-correo-de-prueba', function () {
    $datos = [
        'nombre' => 'Juan Pérez',
        'mensaje' => 'Este es un mensaje de prueba.'
    ];

    // Use a valid email address (e.g., [email protected])
    Mail::to('borjaahedo@gmail.com')->send(new TestEmail($datos));

    return '¡E-MAIL WYSŁANY!';
});

// Existing resources
Route::resource('campaign-plannings', CampaignPlanningController::class);
Route::resource('campaign-reports', CampaignReportController::class);

// Add the new EmailContact resource
Route::resource('email-contacts', EmailContactController::class);

Route::get('/import-contacts', [EmailContactFileController::class, 'showImportForm'])->name('contacts.import.form');
Route::post('/import-contacts', [EmailContactFileController::class, 'import'])->name('contacts.import');

Route::get('/test-email', function() {
    try {
        Mail::to('admin@admin.com')->send(new \App\Mail\TestEmail());
        return 'Email sent successfully';
    } catch (\Exception $e) {
        return 'Error: '.$e->getMessage();
    }
});

Route::get('/test-smtp', function() {
    $provider = App\Models\Provider::first();
    
    $result = App\Support\MailConfigHelper::testConnection([
        'host' => $provider->smtp_host,
        'port' => $provider->smtp_port,
        'encryption' => $provider->smtp_encryption,
        'username' => $provider->smtp_username,
        'password' => $provider->smtp_password,
    ]);

    dd($result ? 'Connection successful' : 'Connection failed');
});

Route::get('/unsubscribe/{contact}/{campaign}', function ($contactId, $campaignId) {
    // Handle unsubscribe logic here
    return "You have been unsubscribed from this campaign";
})->name('unsubscribe');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';