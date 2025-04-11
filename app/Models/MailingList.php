<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\EmailContact;

class MailingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'is_public',
        'status'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * @deprecated Use emailContacts() instead
     */
    public function contacts()
    {
        // You can either remove this method or keep it with a deprecation notice
        // that logs a warning when used
        \Log::warning('The contacts() relationship is deprecated. Use emailContacts() instead.');
        
        // Forward to the correct relationship to maintain backward compatibility
        return $this->emailContacts();
    }

    /**
     * The email contacts that belong to this mailing list.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function emailContacts()
    {
        return $this->belongsToMany(EmailContact::class, 'email_contact_mailing_list')
            ->withPivot('status', 'subscribed_at', 'unsubscribed_at')
            ->withTimestamps();
    }

    // Keep your other existing methods
    
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
}