<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingList extends Model
{
    use HasFactory;

    // Add your fillable properties and other model attributes

    /**
     * The email contacts that belong to this mailing list.
     */
    public function emailContacts()
    {
        return $this->belongsToMany(EmailContact::class, 'list_contact_relationships', 'list_id', 'contact_id')
                    ->withPivot('subscription_date', 'status');
    }
}