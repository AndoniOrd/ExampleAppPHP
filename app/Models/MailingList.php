<?php

namespace App\Models;

use Database\Factories\EmailContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\EmailContact;


class MailingList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'is_public',
        'status'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    /**
     * The email contacts that belong to this mailing list.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_mailing_list', 'mailing_list_id', 'contact_id')
            ->using(ContactMailingList::class)
            ->withPivot('status', 'subscription_date')
            ->withTimestamps();
    }

    /**
     * Scope for active mailing lists
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for public mailing lists
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
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

    public function emailContacts()
    {
        return $this->belongsToMany(EmailContact::class, 'email_contact_mailing_list')
            ->withPivot('status', 'subscribed_at', 'unsubscribed_at')
            ->withTimestamps();
    }
}