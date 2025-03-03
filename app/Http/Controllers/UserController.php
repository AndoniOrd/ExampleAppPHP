<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }
    
    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        
        // Hash the password before storing it
        $data['password'] = Hash::make($data['password']);
        
        // Create the user record
        User::create($data);
        
        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }
    
    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
    
    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }
    
    /**
     * Update the specified user in storage.
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        // If password is provided in update, hash it
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        
        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }
    
    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
