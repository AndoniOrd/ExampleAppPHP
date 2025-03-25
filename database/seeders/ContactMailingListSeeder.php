<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\MailingList;

class ContactMailingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contact_mailing_list')->insert([
            [
                'contact_id' => 1,          // NOT email_contact_id
                'mailing_list_id' => 11,
                'status' => 'subscribed',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}