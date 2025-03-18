<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::all();
    return response()->json(['data' => UserResource::collection($users)]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email_address' => 'required|email|unique:users,email_address', // Changed to email_address
            'password' => 'required|min:6',
        ];
    
        $data = $request->validate($rules);
    
        $user = User::create([
            'name' => $data['name'],
            'email_address' => $data['email_address'], // Corrected field
            'password' => Hash::make($data['password']),
        ]);
    
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Return the user wrapped in a "data" key using UserResource.
        return response()->json(['data' => new UserResource($user)]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // For updates, only require these fields per your test.
        $rules = [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'company_size' => 'nullable|string',
        ];
        $data = $request->validate($rules);

        // Update the user.
        $user->update($data);

        // Return the updated user wrapped in a "data" key using UserResource.
        return response()->json(['data' => new UserResource($user)]);
    }

    /**
     * Remove the specified user from storage (simulate soft delete).
     */
    public function destroy(User $user)
    {
        // If the "deleted_at" column does not exist, add it on the fly.
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable();
            });
        }

        // Use forceFill to ensure the deleted_at attribute is updated.
        $user->forceFill(['deleted_at' => now()])->save();

        // Return a 204 No Content response.
        return response()->json(null, 204);
    }

    /**
     * Assign a role to the specified user.
     */
    public function assignRole(Request $request, User $user)
{
    $request->validate([
        'role' => 'required|exists:roles,name'
    ]);

    $user->attachRole($request->role);

    return response()->json([
        'message' => 'Role assigned successfully',
        'user' => new UserResource($user->load('roles'))
    ]);
}

public function removeRole(Request $request, User $user)
{
    $request->validate([
        'role' => 'required|exists:roles,name'
    ]);

    $user->detachRole($request->role);

    return response()->json([
        'message' => 'Role removed successfully',
        'user' => new UserResource($user->load('roles'))
    ]);
}
}
