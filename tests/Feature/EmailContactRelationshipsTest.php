<?php

namespace Tests\Feature\Models;

use App\Models\EmailContact;
use App\Models\ListContactRelationship;
use App\Models\MailingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


class EmailContactRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function email_contact_can_belong_to_multiple_mailing_lists()
    {
        $emailContact = EmailContact::factory()->create();
        $mailingList1 = MailingList::factory()->create();
        $mailingList2 = MailingList::factory()->create();

        // Attach through the relationship
        $emailContact->mailingLists()->attach($mailingList1->id, [
            'subscription_date' => $date1 = now()->subDays(5)->toDateTimeString(),
            'status' => 'subscribed'
        ]);
        
        $emailContact->mailingLists()->attach($mailingList2->id, [
            'subscription_date' => $date2 = now()->subDays(3)->toDateTimeString(),
            'status' => 'unsubscribed'
        ]);

        // Refresh relationships
        $emailContact->load('mailingLists');
        
        // Assert relationships
        $this->assertCount(2, $emailContact->mailingLists);
        $this->assertEqualsCanonicalizing(
            [$mailingList1->id, $mailingList2->id],
            $emailContact->mailingLists->pluck('id')->toArray()
        );

        // Verify pivot data as strings
        $firstPivot = $emailContact->mailingLists->first()->pivot;
        $this->assertEquals('subscribed', $firstPivot->status);
        $this->assertEquals($date1, $firstPivot->subscription_date);
    }

    #[Test]
    public function mailing_list_can_access_contacts()
    {
        $mailingList = MailingList::factory()->create();
        $emailContact1 = EmailContact::factory()->create();
        $emailContact2 = EmailContact::factory()->create();

        // Use the proper relationship through EmailContact
        $emailContact1->mailingLists()->attach($mailingList->id, [
            'subscription_date' => now()->toDateTimeString(),
            'status' => 'subscribed'
        ]);
        
        $emailContact2->mailingLists()->attach($mailingList->id, [
            'subscription_date' => now()->toDateTimeString(),
            'status' => 'pending'
        ]);

        // Verify through the pivot table
        $relationships = ListContactRelationship::where('list_id', $mailingList->id)->get();
        
        $this->assertCount(2, $relationships);
        $this->assertEqualsCanonicalizing(
            [$emailContact1->id, $emailContact2->id],
            $relationships->pluck('contact_id')->toArray()
        );
    }
}