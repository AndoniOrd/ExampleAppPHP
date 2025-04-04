<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Contact as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MailingList> $mailingLists
 * @property-read int|null $mailing_lists_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\ContactFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact query()
 * @mixin \Eloquent
 */
class Contact extends Model
{

 /** @use HasFactory<\Database\Factories\UserFactory> */
 use HasFactory, Notifiable;
     
    public $timestamps = false;

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
    return $this->belongsToMany(MailingList::class, 'contact_mailing_list', 'contact_id', 'mailing_list_id')
        ->using(ContactMailingList::class)
        ->withPivot('status', 'subscription_date')
        ->withTimestamps();
}
    protected function casts(): array
    {
        return [
            'opt_in_confirmation' => 'boolean',
            'custom_fields' => 'array',
            'opt_in_date' => 'date',
            'creation_date' => 'date',
            'last_updated_date' => 'date',
        ];
    }
}