<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class FixPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Define correct permissions
        $permissions = [
            'view-user',
            'create-user',
            'update-user',
            'delete-user'
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName], [
                'display_name' => ucwords(str_replace('-', ' ', $permName)),
                'description' => 'Ability to ' . str_replace('-', ' ', $permName)
            ]);
        }

        // Get or create admin role
        $adminRole = Role::where('name', 'admin')->first();

        // Attach all permissions to admin role
        $adminRole->syncPermissions(Permission::whereIn('name', $permissions)->get());
    }
}