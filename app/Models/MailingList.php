<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\EmailContact;
use App\Models\User;
use Carbon\Carbon;

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

    // IMPORTANT: Override the create method to ensure creation_date is set
    public static function create(array $attributes = [])
    {
        // Force set creation_date and last_updated_date with current timestamp
        $attributes['creation_date'] = Carbon::now();
        $attributes['last_updated_date'] = Carbon::now();
        
        return parent::create($attributes);
    }

    // Also handle it in boot method as backup
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Force set creation_date with current timestamp including time
            if (empty($model->creation_date)) {
                $model->creation_date = Carbon::now();
            }
            if (empty($model->last_updated_date)) {
                $model->last_updated_date = Carbon::now();
            }
        });
        
        static::updating(function ($model) {
            // Update last_updated_date when updating
            $model->last_updated_date = Carbon::now();
        });
    }

    // Your existing relationships...
    public function emailContacts(): BelongsToMany
    {
        return $this->belongsToMany(EmailContact::class, 'email_contact_mailing_list')
            ->withPivot('status', 'subscribed_at', 'unsubscribed_at')
            ->withTimestamps();
    }

    public function subscribedContacts()
    {
        return $this->emailContacts()
            ->wherePivot('status', 'subscribed')
            ->wherePivotNull('unsubscribed_at');
    }

    public function unsubscribedContacts()
    {
        return $this->emailContacts()
            ->wherePivot('status', 'unsubscribed')
            ->orWherePivotNotNull('unsubscribed_at');
    }

    public function addContact(EmailContact $contact, array $attributes = [])
    {
        $defaults = [
            'subscription_date' => now(),
            'status' => 'subscribed'
        ];

        return $this->emailContacts()->attach($contact->id, array_merge($defaults, $attributes));
    }

    public function removeContact(EmailContact $contact)
    {
        return $this->emailContacts()->detach($contact->id);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}