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

 /**
 * @OA\Schema(
 *     schema="CampaignPlanning",
 *     title="Campaign Planning",
 *     description="Campaign Planning model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Campaign for spring products"),
 *     @OA\Property(property="email_template_id", type="integer", example=5),
 *     @OA\Property(property="mailing_list_id", type="integer", example=3),
 *     @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00"),
 *     @OA\Property(property="time_zone", type="string", example="America/New_York"),
 *     @OA\Property(property="status_status_type", type="string", enum={"draft", "scheduled", "processing", "completed"}, example="scheduled"),
 *     @OA\Property(property="scheduled_by", type="integer", example=10),
 *     @OA\Property(property="send_from_email", type="string", format="email", example="marketing@example.com"),
 *     @OA\Property(property="send_from_name", type="string", example="Marketing Team"),
 *     @OA\Property(property="reply_to_email", type="string", format="email", example="support@example.com"),
 *     @OA\Property(property="tracking_options", type="string", example="open_click"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-01 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-10 15:30:00")
 * )
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