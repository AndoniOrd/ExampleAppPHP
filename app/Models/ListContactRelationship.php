<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListContactRelationship extends Model
{
    use HasFactory;
    protected $table = 'list_contact_relationships';
    public $timestamps = false;
    
    protected $fillable = [
        'list_id',
        'contact_id',
        'subscription_date',
        'status',
    ];

    public function definition()
{
    return [
        'list_id' => MailingList::factory(),
        'contact_id' => EmailContact::factory(),
        'status' => 'subscribed', // Explicit status
        'subscription_date' => now(),
    ];
}

    
    // Define inverse relationships if you need direct access.
    public function mailingList()
    {
        return $this->belongsTo(MailingList::class, 'list_id');
    }
    
    public function emailContact()
    {
        // Update the model class name if necessary to match your actual model for emailContacts
        return $this->belongsTo(EmailContact::class, 'contact_id');
    }
}