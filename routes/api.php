<?php

use App\Http\Controllers\Api\CampaignPlanningApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\api\UserApiController;
use App\Http\Controllers\CampaignReportController;
use App\Http\Controllers\EmailTemplatesController;
use App\Http\Controllers\ListContactRelationshipController;
use App\Http\Controllers\Api\EmailContactApiController;

// Public routes (no authentication required)
Route::post('/login', [UserApiController::class, 'login']);
Route::post('/generate-token', [TokenController::class, 'generateToken']);

// Authenticated routes (require Sanctum authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes with specific permissions
    Route::get('/users', [UserApiController::class, 'index'])
        ->middleware('permission:view-user');
    Route::post('/users', [UserApiController::class, 'store'])
        ->middleware('permission:create-user');
    Route::get('/users/{id}', [UserApiController::class, 'show'])
        ->middleware('permission:view-user');
    Route::put('/users/{id}', [UserApiController::class, 'update'])
        ->middleware('permission:update-user');
    Route::delete('/users/{id}', [UserApiController::class, 'destroy'])
        ->middleware('permission:delete-user');
    
    // Other routes
    Route::apiResource('email_templates', EmailTemplatesController::class);
    Route::apiResource('list-contact-relationships', ListContactRelationshipController::class);
    Route::post('/import-email-contacts', [EmailContactApiController::class, 'import']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('campaign-plannings', CampaignPlanningApiController::class);
    });
});