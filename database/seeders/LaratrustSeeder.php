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
        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator with full access',
        ]);

        $editorRole = Role::create([
            'name' => 'editor',
            'display_name' => 'Editor',
            'description' => 'Editor with content management access',
        ]);

        $viewerRole = Role::create([
            'name' => 'viewer',
            'display_name' => 'Viewer',
            'description' => 'Viewer with read-only access',
        ]);

        // Create permissions
        $createUserPerm = Permission::create([
            'name' => 'create-user',
            'display_name' => 'Create User',
            'description' => 'Create new users',
        ]);

        $editUserPerm = Permission::create([
            'name' => 'edit-user',
            'display_name' => 'Edit User',
            'description' => 'Edit existing users',
        ]);

        $deleteUserPerm = Permission::create([
            'name' => 'delete-user',
            'display_name' => 'Delete User',
            'description' => 'Delete users',
        ]);

        $viewUserPerm = Permission::create([
            'name' => 'view-user',
            'display_name' => 'View User',
            'description' => 'View user details',
        ]);

        // Assign permissions to roles
        $adminRole->syncPermissions([
            $createUserPerm,
            $editUserPerm,
            $deleteUserPerm,
            $viewUserPerm,
        ]);

        $editorRole->syncPermissions([
            $editUserPerm,
            $viewUserPerm,
        ]);

        $viewerRole->syncPermissions([
            $viewUserPerm,
        ]);

        // Create admin user
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

// Attach admin role to admin user using the roles relationship
$admin->roles()->attach($adminRole->id);  // This uses Laravel's relationship methods instead
    }
}