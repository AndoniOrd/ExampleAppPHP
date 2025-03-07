<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

//Route::apiResource('users', UserController::class);
Route::middleware('auth:sanctum')->group( function () {
    Route::get('users', [UserController::class,'index'])
    ->middleware('role:admin')
    ->name('users.list');
});

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $token = $request->user()->createToken('auth-token')->plainTextToken;
    
    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer'
    ]);
});





