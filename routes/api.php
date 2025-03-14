<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\CampaignReportController;
use App\Http\Controllers\EmailTemplatesController;
use App\Http\Controllers\ListContactRelationshipController;
use App\Http\Controllers\Api\EmailContactApiController;
use Illuminate\Http\Request;

// Public routes
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email_address' => 'required|email', // Changed to match your DB column
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    return response()->json([
        'access_token' => $request->user()->createToken('api-token')->plainTextToken,
        'token_type' => 'Bearer',
    ]);
});

Route::post('/import-email-contacts', [EmailContactApiController::class, 'import']);

// Protected routes with Sanctum and permissions
Route::middleware(['auth:sanctum'])->group(function () {
    // Consolidated users resource with permissions
    Route::prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index'])
            ->middleware('permission:view-users')
            ->name('users.index');
            
        Route::post('/', [UserApiController::class, 'store'])
            ->middleware('permission:create-users')
            ->name('users.store');
            
        Route::get('/{id}', [UserApiController::class, 'show'])
            ->middleware('permission:view-users')
            ->name('users.show');
            
        Route::put('/{id}', [UserApiController::class, 'update'])
            ->middleware('permission:edit-users')
            ->name('users.update');
            
        Route::delete('/{id}', [UserApiController::class, 'destroy'])
            ->middleware('permission:delete-users')
            ->name('users.destroy');
    });

    // Other API resources
    Route::apiResource('campaign-reports', CampaignReportController::class);
    Route::apiResource('email_templates', EmailTemplatesController::class);
    Route::apiResource('list-contact-relationships', ListContactRelationshipController::class);
});