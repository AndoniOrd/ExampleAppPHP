<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

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