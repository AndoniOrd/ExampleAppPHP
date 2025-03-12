<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailContact extends Model
{
    use HasFactory;

    protected $table = 'email_contacts'; // Explicit table name

    protected $casts = [
        'opt_in_date' => 'date',
        'opt_in_confirmation' => 'boolean',
        'custom_fields' => 'array',
        'creation_date' => 'datetime', // Changed to datetime
        'last_updated_date' => 'datetime', // Changed to datetime
    ];

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
}