<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailContact extends Model
{
    use HasFactory;
    
    // Updated to use the camelCase table name
    protected $table = 'emailContacts';
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
        'last_updated_date'
    ];
    
    protected $casts = [
        'custom_fields' => 'array',
        'opt_in_confirmation' => 'boolean',
    ];
    
    public function listRelationships()
    {
        return $this->hasMany(ListContactRelationship::class, 'contact_id');
    }
    
    public function mailingLists()
    {
        return $this->belongsToMany(MailingList::class, 'list_contact_relationships', 'contact_id', 'list_id')
            ->withPivot('subscription_date', 'status');
    }
}