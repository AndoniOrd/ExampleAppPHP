<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailContact extends Model
{
    use HasFactory;

    protected $table = 'email_contacts';

    protected $casts = [
        'opt_in_date' => 'date',
        'opt_in_confirmation' => 'boolean',
        'custom_fields' => 'array',
        'creation_date' => 'datetime',
        'last_updated_date' => 'datetime',
    ];

    protected $fillable = [
        'email',
        'name',
        'last_name',
        'status',
        'source',
        'opt_in_date',
        'opt_in_confirmation',
        'custom_fields',
        'creation_date',
        'last_updated_date',
    ];

    public function mailingLists()
    {
        return $this->belongsToMany(MailingList::class, 'email_contact_mailing_list')
            ->withPivot('status', 'subscribed_at', 'unsubscribed_at')
            ->withTimestamps();
    }
}