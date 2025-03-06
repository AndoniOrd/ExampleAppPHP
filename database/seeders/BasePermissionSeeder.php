<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


abstract class BasePermissionSeeder extends Seeder
{
    /**
     * The model name to use in permission naming (e.g., "users" or "customers").
     */
    protected string $modelName;

    /**
     * Array of roles that should receive these permissions.
     */
    protected array $roles = [];

    /**
     * CRUD actions.
     */
    protected array $actions = ['create', 'read', 'update', 'delete'];

    /**
     * Execute the seeder.
     */
    public function run()
    {
        $this->seedPermissions();
    }

    /**
     * Create permissions for each action and assign them to the provided roles.
     */
    protected function seedPermissions()
    {
        foreach ($this->actions as $action) {
            // Construct permission name in the format "model.action" (e.g., "users.create")
            $permissionName = strtolower($this->modelName) . '.' . $action;

            // Create or retrieve the permission
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            // Assign the permission to each specified role
            foreach ($this->roles as $roleName) {
                // Find the role by name or create it if it doesn't exist
                $role = Role::firstOrCreate(['name' => $roleName]);

                // Assign permission if not already assigned
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
