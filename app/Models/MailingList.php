<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\EmailContact;
use App\Models\User;

class MailingList extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'description',
    'creation_date',
    'status',
    'type',
    'tags',
    'last_updated_date',
    'owner_id',
    'created_by'
];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'creation_date' => 'datetime',
        'last_updated_date' => 'datetime',
    ];

    /**
     * @deprecated Use emailContacts() instead
     */
    public function contacts()
    {
        \Log::warning('The contacts() relationship is deprecated. Use emailContacts() instead.');
        return $this->emailContacts();
    }

    /**
     * The email contacts that belong to this mailing list.
     */
    public function emailContacts(): BelongsToMany
    {
        return $this->belongsToMany(EmailContact::class, 'email_contact_mailing_list')
            ->withPivot('status', 'subscribed_at', 'unsubscribed_at')
            ->withTimestamps();
    }

    /**
     * Get only subscribed contacts
     */
    public function subscribedContacts()
    {
        return $this->emailContacts()
            ->wherePivot('status', 'subscribed')
            ->wherePivotNull('unsubscribed_at');
    }

    /**
     * Get only unsubscribed contacts
     */
    public function unsubscribedContacts()
    {
        return $this->emailContacts()
            ->wherePivot('status', 'unsubscribed')
            ->orWherePivotNotNull('unsubscribed_at');
    }

    /**
     * Add a contact to the mailing list
     */
    public function addContact(EmailContact $contact, array $attributes = [])
    {
        $defaults = [
            'subscription_date' => now(),
            'status' => 'subscribed'
        ];

        return $this->emailContacts()->attach($contact->id, array_merge($defaults, $attributes));
    }

    /**
     * Remove a contact from the mailing list
     */
    public function removeContact(EmailContact $contact)
    {
        return $this->emailContacts()->detach($contact->id);
    }

    /**
     * Get the owner of the mailing list
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the creator of the mailing list
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
