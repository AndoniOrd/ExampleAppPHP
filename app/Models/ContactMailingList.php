<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ContactMailingList extends Pivot
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_mailing_list';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'mailing_list_id',
        'status',          // Add status to fillable attributes
        'subscription_date' // Add subscription date if needed
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'subscription_date' => 'datetime',
        'status' => 'string'
    ];

    /**
     * Default status values
     */
    protected $attributes = [
        'status' => 'unsubscribed' // Set a default status
    ];

    /**
     * Scope to find subscribed contacts
     */
    public function scopeSubscribed($query)
    {
        return $query->where('status', 'subscribed');
    }
}