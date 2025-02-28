<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailingList extends Model
{
    // Protect attributes from mass-assignment unless explicitly allowed
    protected $fillable = [
        'name',
        'description',
        'creation_date',
        'last_updated_date',
        'owner_id', // Foreign key
        'status',
        'type',
        'tags'
    ];
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
