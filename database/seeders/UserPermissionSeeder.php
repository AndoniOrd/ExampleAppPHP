<?php

namespace Database\Seeders;

class UserPermissionSeeder extends BasePermissionSeeder
{
    protected string $modelName = 'users';
    protected array $roles = ['admin', 'editor', 'viewer'];
    protected array $actions = ['view']; // Add other actions as needed
}
