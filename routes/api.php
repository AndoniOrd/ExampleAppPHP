<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\Http\Controllers\CampaignReportController;
use App\Http\Controllers\EmailTemplatesController;
use App\Http\Controllers\ListContactRelationshipController;
use App\Http\Controllers\Api\EmailContactApiController;
use App\Http\Controllers\Api\UserApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', [UserController::class, 'index'])
        ->middleware('permission:view-users')
        ->name('users.list');
    
    Route::post('users', [UserController::class, 'store'])
        ->middleware('permission:create-users')
        ->name('users.create');
    
    Route::put('users/{user}', [UserController::class, 'update'])
        ->middleware('permission:edit-users')
        ->name('users.update');
    
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:delete-users')
        ->name('users.delete');
});

Route::apiResource('campaign-reports', CampaignReportController::class);
Route::apiResource('email_templates', EmailTemplatesController::class);
Route::apiResource('list-contact-relationships', ListContactRelationshipController::class);
Route::post('/import-email-contacts', [EmailContactApiController::class, 'import']);

// Login route remains unchanged
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $token = $request->user()->createToken('auth-token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', [UserApiController::class, 'index']);
    Route::post('users', [UserApiController::class, 'store']);
    Route::get('users/{id}', [UserApiController::class, 'show']);
    Route::put('users/{id}', [UserApiController::class, 'update']);
    Route::delete('users/{id}', [UserApiController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserApiController::class);
});