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
        $admin = User::create([
            'name'     => 'John',
            'last_name'      => 'Doe',
            'email'  => 'john.doe@example.com',
            'password'       => Hash::make('securepassword'),
            'phone_number'   => '123-456-7890',
            'account_status' => 'active',
            'creation_date'  => now(),
            'company_name'   => 'Doe Enterprises',
            'company_address'=> '123 Business Rd, City, Country',
            'vat_tax_id'     => 'VAT123456789',
            'industry'       => 'Software Development',
            'company_size'   => 50,
            'website'        => 'https://doeenterprises.com',
        ]);
    
        $adminRole = Role::where('name', 'admin')->first();
        // Use the standard Laravel relationship method
        $admin->roles()->attach($adminRole->id);
    }
}