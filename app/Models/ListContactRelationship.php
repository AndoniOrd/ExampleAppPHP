<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListContactRelationship extends Model
{
    protected $table = 'list_contact_relationships';
    public $timestamps = false;
    
    protected $fillable = [
        'list_id',
        'contact_id',
        'subscription_date',
        'status',
    ];
    
    // Define inverse relationships if you need direct access.
    public function mailingList()
    {
        return $this->belongsTo(MailingList::class, 'list_id');
    }
    
    public function emailContact()
    {
        return $this->belongsTo(EmailContact::class, 'contact_id');
    }
}
