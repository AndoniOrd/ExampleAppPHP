<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'email_address',
        'first_name',
        'last_name',
        'status',
        'source',
        'opt_in_date',
        'opt_in_confirmation',
        'custom_fields',
        'creation_date',
        'last_updated_date',
    ];

    protected $casts = [
        'opt_in_confirmation' => 'boolean',
        'custom_fields' => 'array',
        'opt_in_date' => 'date',
        'creation_date' => 'date',
        'last_updated_date' => 'date',
    ];

    public function mailingLists()
    {
        return $this->belongsToMany(MailingList::class);
    }
}