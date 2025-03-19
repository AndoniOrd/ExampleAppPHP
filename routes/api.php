<?php

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
Route::middleware(['auth:sanctum', 'permission:view-user'])->group(function () {
    Route::apiResource('users', UserApiController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    // Add other routes as needed with appropriate middleware
});
    
Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:view-users'); // or another permission name
    
    // Email contacts import
    Route::post('/import-email-contacts', [EmailContactApiController::class, 'import']);
    
    // Other resources
    Route::apiResource('email_templates', EmailTemplatesController::class);
    Route::apiResource('list-contact-relationships', ListContactRelationshipController::class);
    
// Route::prefix('users')->group(function () {
//     Route::get('/', [UserController::class, 'index'])
//         ->middleware('permission:view-user')
//         ->name('users.list');
        
//     Route::post('/', [UserController::class, 'store'])
//         ->middleware('permission:create-users')
//         ->name('users.create');
        
//     Route::delete('/{user}', [UserController::class, 'destroy'])
//         ->middleware('permission:delete-users')
//         ->name('users.delete');
// });

   // Route::middleware(['auth', 'role:admin'])->get('/api/users', [UserController::class, 'index']);
