<?php

namespace Database\Seeders;

class UserPermissionSeeder extends BasePermissionSeeder
{
    protected string $modelName = 'users';
    protected array $roles = ['admin', 'editor', 'viewer'];
}
