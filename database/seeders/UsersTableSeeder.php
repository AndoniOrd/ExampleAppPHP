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
            'first_name'     => 'Andoni',
            'last_name'      => 'Ordonez',
            'email_address'  => 'andoniordonez755@gmail.com',
            'password'       => Hash::make('abcd*1234'),
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