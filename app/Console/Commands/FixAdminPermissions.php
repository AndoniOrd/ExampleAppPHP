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
            Permission::firstOrCreate([
                'name' => $permName,
                'guard_name' => 'web'  // The default Laravel guard
            ]);
        }

        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // Attach all permissions to admin role
            $adminRole->syncPermissions(Permission::whereIn('name', $permissions)->get());
        } else {
            $this->command->error('Admin role not found!');
        }
    }
}