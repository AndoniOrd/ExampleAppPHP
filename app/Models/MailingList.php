<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(
            EmailContact::class,
            'list_contact_relationships',  // Pivot table name
            'list_id',                    // Foreign key on pivot table
            'contact_id'                   // Related key on pivot table
        )->withPivot([
            'subscription_date',
            'status',
            'unsubscribed_at'
        ])->withTimestamps();
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
}