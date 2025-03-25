<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica si la base de datos no es SQLite antes de deshabilitar las claves forÃ¡neas
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        Role::truncate();
        Permission::truncate();

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Crear roles
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

        // Crear permisos
        $createUserPerm = Permission::create([
            'name' => 'create-user',
            'guard_name' => 'web',
        ]);

        $editUserPerm = Permission::create([
            'name' => 'update-user',
            'guard_name' => 'web',
        ]);

        $deleteUserPerm = Permission::create([
            'name' => 'delete-user',
            'guard_name' => 'web',
        ]);

        $viewUserPerm = Permission::create([
            'name' => 'view-user',
            'guard_name' => 'web',
        ]);

        // Asignar permisos a roles
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

        // Crear usuario administrador
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email_address' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '123-456-7890',
            'account_status' => 'active',
            'creation_date' => now(),
        ]);

        // Asignar rol de administrador al usuario
        $admin->roles()->attach($adminRole->id);
    }
}
