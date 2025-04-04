<?php

namespace App\Traits;

use Laratrust\Models\Permission;
use Laratrust\Models\Role;
use Illuminate\Support\Collection;

trait LaratrustUserTrait
{
    /**
     * Get all of the roles that the user has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    /**
     * Get all of the permissions that the user has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }

    /**
     * Attach a role to the user.
     *
     * @param string|\Laratrust\Models\Role $role
     * @return void
     */
    public function attachRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }
        
        $this->roles()->attach($role);
    }

    /**
     * Attach a permission to the user.
     *
     * @param string|\Laratrust\Models\Permission $permission
     * @return void
     */
    public function attachPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        $this->permissions()->attach($permission);
    }

    /**
     * Determine if the user has a given role.
     *
     * @param string|\Laratrust\Models\Role $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        
        return $this->roles->contains($role);
    }

    /**
     * Determine if the user has a given permission.
     *
     * @param string|\Laratrust\Models\Permission $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }

        return $this->permissions->contains($permission);
    }

    /**
     * Sync the roles for the user.
     *
     * @param array $roles
     * @return void
     */
    public function syncRoles(array $roles)
    {
        $this->roles()->sync($roles);
    }

    /**
     * Sync the permissions for the user.
     *
     * @param array $permissions
     * @return void
     */
    public function syncPermissions(array $permissions)
    {
        $this->permissions()->sync($permissions);
    }

    /**
     * Detach all roles from the user.
     *
     * @return void
     */
    public function detachRoles()
    {
        $this->roles()->detach();
    }

    /**
     * Detach all permissions from the user.
     *
     * @return void
     */
    public function detachPermissions()
    {
        $this->permissions()->detach();
    }
}
