<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TrackingOptions;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $email_template_id
 * @property string $mailing_list_id
 * @property \Illuminate\Support\Carbon $scheduled_time
 * @property string $time_zone
 * @property string $status_status_type
 * @property \Illuminate\Support\Carbon $creation_date
 * @property string $scheduled_by
 * @property string $send_from_email
 * @property string $send_from_name
 * @property string $reply_to_email
 * @property TrackingOptions $tracking_options
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EmailTemplates|null $emailTemplate
 * @property-read \App\Models\MailingList|null $mailingList
 * @property-read \App\Models\User|null $scheduledBy
 */
class CampaignPlanning extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'email_template_id',
        'mailing_list_id',
        'scheduled_time',
        'time_zone',
        'status_status_type',
        'creation_date',
        'scheduled_by',
        'send_from_email',
        'send_from_name',
        'reply_to_email',
        'tracking_options',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'creation_date' => 'date',
        'tracking_options' => TrackingOptions::class,
    ];

    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplates::class, 'email_template_id');
    }

    public function mailingList()
    {
        return $this->belongsTo(MailingList::class, 'mailing_list_id');
    }

    public function scheduledBy()
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }
}