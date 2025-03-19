<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        Permission::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator with full access',
            'guard_name' => 'web',
        ]);
    
        $editorRole = Role::create([
            'name' => 'editor',
            'display_name' => 'Editor',
            'description' => 'Editor with content management access',
            'guard_name' => 'web',
        ]);
    
        $viewerRole = Role::create([
            'name' => 'viewer',
            'display_name' => 'Viewer',
            'description' => 'Viewer with read-only access',
            'guard_name' => 'web',
        ]);
    
        // Create permissions
        $createUserPerm = Permission::create([
            'name' => 'create-user',
            'guard_name' => 'web', // Add this line
        ]);
    
        $editUserPerm = Permission::create([
            'name' => 'update-user', 
            'guard_name' => 'web',
        ]);
    
        $deleteUserPerm = Permission::create([
            'name' => 'delete-user',
            'guard_name' => 'web', // Add this line
        ]);
    
        $viewUserPerm = Permission::create([
            'name' => 'view-user',
            'guard_name' => 'web', // Add this line
        ]);
    
        // Assign permissions to roles
        $adminRole->syncPermissions([
            $createUserPerm,  // create-user
            $editUserPerm,    // Debe ser update-user (corregido arriba)
            $deleteUserPerm,  // delete-user
            $viewUserPerm,    // view-user
        ]);
    
        $editorRole->syncPermissions([
            $editUserPerm,
            $viewUserPerm,
        ]);
    
        $viewerRole->syncPermissions([
            $viewUserPerm,
        ]);
    
        // Create admin user
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email_address' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '123-456-7890',
            'account_status' => 'active',
            'creation_date' => now(),
        ]);
    
        // Attach admin role to admin user
        $admin->roles()->attach($adminRole->id);
    }
}