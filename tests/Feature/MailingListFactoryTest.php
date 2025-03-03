<?php

namespace Tests\Feature;

use App\Models\MailingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailingListFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_active_mailing_list()
    {
        $mailingList = MailingList::factory()->create([
            'name' => 'Promotional Emails',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('mailing_lists', [
            'id' => $mailingList->id,
            'name' => 'Promotional Emails',
            'status' => 'active',
        ]);
    }

    public function test_it_creates_a_draft_mailing_list()
    {
        $mailingList = MailingList::factory()->create([
            'name' => 'Newsletter Subscribers',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('mailing_lists', [
            'id' => $mailingList->id,
            'name' => 'Newsletter Subscribers',
            'status' => 'draft',
        ]);
    }

    public function test_it_creates_a_promotions_mailing_list()
    {
        $mailingList = MailingList::factory()->create([
            'name' => 'Internal Team Updates',
            'type' => 'promotions',
        ]);

        $this->assertDatabaseHas('mailing_lists', [
            'id' => $mailingList->id,
            'name' => 'Internal Team Updates',
            'type' => 'promotions',
        ]);
    }
}
