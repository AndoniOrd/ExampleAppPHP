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
        // Create the user
        $user = User::create([
            'first_name'     => 'John',
            'last_name'      => 'Doe',
            'email_address'  => 'john.doe@example.com',
            'password'       => Hash::make('securepassword'),
            'phone_number'   => '123-456-7890',
            'account_status' => 'active',
            'company_name'   => 'Doe Enterprises',
            'company_address'=> '123 Business Rd, City, Country',
            'vat_tax_id'     => 'VAT123456789',
            'industry'       => 'Software Development',
            'company_size'   => 50,
            'website'        => 'https://doeenterprises.com',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    
        // Get the "admin" role
        $role = Role::where('name', 'admin')->first();
    
        if ($role) {
            // Detach all roles before attaching the new one
            $user->detachRoles();
            
            // Attach the "admin" role explicitly and set user_type
            $user->attachRole($role, [
                'user_type' => get_class($user),  // Set the user_type to the User model class name
            ]);
        }
    }
}
