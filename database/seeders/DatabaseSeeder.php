<?php

namespace Database\Seeders;

use App\Models\MailingList;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // database/seeders/DatabaseSeeder.php
public function run(): void
{
    User::factory()->create([
        'name' => 'damikozi223@gmail.com',
        'password' => bcrypt('password'),
    ]);
    User::factory(10)->create();
    MailingList::factory(10)->create();

    $this->call([
        AllPermissionSeeder::class, // This already calls UserPermissionSeeder and MailingListPermissionSeeder
        // Remove BasePermissionSeeder::class from here
        CampaignPlanningSeeder::class,
        //CampaignReportsSeeder::class,
        ContactsTableSeeder::class,
        EmailContactSeeder::class,
        EmailTemplatesSeeder::class,
        ListContactRelationshipSeeder::class,
        MailingListPermissionSeeder::class,
        MailingListSeeder::class,


        LaratrustSeeder::class,
        FixPermissionsSeeder::class,
        UsersTableSeeder::class,
        ContactMailingListSeeder::class,
        DictionarySeeder::class,
        ProvidersSeeder::class,
        //StatusDictionarySeeder::class
    ]);
}
}
