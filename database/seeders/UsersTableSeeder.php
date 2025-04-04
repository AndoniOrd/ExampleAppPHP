<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Laratrust\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure roles exist first with guard_name
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ], [
            'display_name' => 'Administrator',
            'description' => 'System administrator with full privileges'
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web'
        ], [
            'display_name' => 'Regular User',
            'description' => 'Standard application user'
        ]);

        // Rest of your user creation code remains the same
        // Create admin user
        $admin = User::create([
            'name'          => 'Andoni',
            'last_name'     => 'Ordonez',
            'email'         => 'andoniordonez755@gmail.com',
            'password'      => Hash::make('abcd*1234'),
            'phone_number'  => '123-456-7890',
            'account_status' => 'active',
            'creation_date' => now(),
            'company_name'  => 'Doe Enterprises',
            'company_address' => '123 Business Rd, City, Country',
            'vat_tax_id'    => 'VAT123456789',
            'industry'      => 'Software Development',
            'company_size'  => 50,
            'website'       => 'https://doeenterprises.com',
        ]);

        $admin->roles()->attach($adminRole->id);

        // Create regular user
        $user = User::create([
            'name'          => 'John',
            'last_name'     => 'Doe',
            'email'         => 'john.doe@example.com',
            'password'      => Hash::make('securepassword'),
            'phone_number'  => '987-654-3210',
            'account_status' => 'active',
            'creation_date' => now(),
            'company_name'  => 'John Doe Co.',
            'company_address' => '456 Main St, Townsville, Country',
            'vat_tax_id'    => 'VAT987654321',
            'industry'      => 'Consulting',
            'company_size'  => 10,
            'website'       => 'https://johndoe.com',
        ]);

        $user->roles()->attach($userRole->id);
    }
}