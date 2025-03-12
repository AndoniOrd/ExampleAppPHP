<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailContact extends Model
{
    use HasFactory;

    protected $casts = [
        'opt_in_date' => 'date',
        'opt_in_confirmation' => 'boolean',
        'custom_fields' => 'array',
        'creation_date' => 'date',
        'last_updated_date' => 'date',
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

    // If you need to handle ENUM values
    protected $enums = [
        'status' => ['active', 'inactive', 'pending'],
        'source' => ['web', 'api', 'manual'],
    ];

    public $timestamps = false; // Since we have custom date columns
}