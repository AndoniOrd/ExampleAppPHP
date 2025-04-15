<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivedEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'message_id',
        'subject',
        'content',
        'sender_email',
        'sender_name',
        'received_at',
        'is_read',
        'has_attachments',
        'headers'
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'is_read' => 'boolean',
        'has_attachments' => 'boolean',
        'headers' => 'array'
    ];

    /**
     * Get the provider that this email was received from
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}