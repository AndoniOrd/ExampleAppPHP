<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @method static \Database\Factories\MailingListFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailingList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailingList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailingList query()
 * @mixin \Eloquent
 */
class MailingList extends Model
{
    use HasFactory;
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

