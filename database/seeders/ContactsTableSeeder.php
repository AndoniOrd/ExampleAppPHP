<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContactsTableSeeder extends Seeder
{
    public function run()
    {
        Contact::create([
            'email' => 'john.doe@example.com',
            'name' => 'John',
            'last_name' => 'Doe',
            'status' => 'subscribed',
            'source' => 'web',
            'opt_in_date' => Carbon::now(),
            'opt_in_confirmation' => true,
            'custom_fields' => json_encode(['referral' => 'newsletter']),
            'creation_date' => Carbon::now(),
            'last_updated_date' => Carbon::now(),
        ]);
    }
}
