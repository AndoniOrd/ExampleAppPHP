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
        // Return users wrapped in a "data" key using UserResource collection.
        return response()->json(['data' => UserResource::collection($users)]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Validate request data.
        $rules = [
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email_address'  => 'required|email|unique:users,email_address',
            'password'       => 'required|string|min:6',
            'phone_number'   => 'nullable|string|max:20',
            'role'           => 'required|in:admin,editor,viewer,user',
            'account_status' => 'required|in:active,inactive,suspended',
            'creation_date'  => 'nullable|date',
            'company_name'   => 'nullable|string|max:255',
            'vat_tax_id'     => 'nullable|string|max:50',
            'company_address'=> 'nullable|string|max:255',
            'industry'       => 'nullable|string|max:255',
            'company_size'   => 'nullable|string',
            'website'        => 'nullable|url',
        ];
        $data = $request->validate($rules);

        // Set default creation_date if not provided.
        if (empty($data['creation_date'])) {
            $data['creation_date'] = now();
        }

        // Save the original role for response purposes.
        $originalRole = $data['role'];
        // If role is "user", map it to "viewer" for database/storage.
        if ($data['role'] === 'user') {
            $data['role'] = 'viewer';
        }

        // Hash the password.
        $data['password'] = Hash::make($data['password']);

        // Create the user.
        $user = User::create($data);

        // Attach the role using Laratrust.
        $user->attachRole($data['role']);

        // Override the role attribute for the JSON response.
        $user->role = $originalRole;

        return response()->json(['data' => new UserResource($user)], 201);
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
        // Validate the role.
        $rules = [
            'role' => 'required|in:admin,editor,viewer,user'
        ];
        $data = $request->validate($rules);

        // Map "user" to "viewer" if necessary.
        $role = $data['role'];
        if ($role === 'user') {
            $role = 'viewer';
        }

        // Attach the role via Laratrust.
        $user->attachRole($role);

        return response()->json(['data' => new UserResource($user)], 200);
    }

    /**
     * Remove a role from the specified user.
     */
    public function removeRole(Request $request, User $user)
    {
        // Validate the role.
        $rules = [
            'role' => 'required|in:admin,editor,viewer,user'
        ];
        $data = $request->validate($rules);

        // Map "user" to "viewer" if necessary.
        $role = $data['role'];
        if ($role === 'user') {
            $role = 'viewer';
        }

        // Detach the role via Laratrust.
        $user->detachRole($role);

        return response()->json(['data' => new UserResource($user)], 200);
    }
}
