<?php

namespace Database\Seeders;

class MailingListPermissionSeeder extends BasePermissionSeeder
{
    /**
     * Model name for permission generation
     */
    protected string $modelName = 'mailing_list';

    /**
     * Roles that should receive all mailing list permissions
     */
    protected array $roles = ['admin', 'manager'];  // Adjust roles as needed

    // Optional: Customize actions if you don't want all CRUD permissions
    // protected array $actions = ['create', 'read', 'update', 'delete'];
}