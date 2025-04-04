<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TrackingOptions;

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
 *     @OA\Property(property="status_type", type="string", enum={"active", "inactive"}, example="active"),
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
        'status_type',
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
        'status_type' => 'boolean', // Cast to boolean for the toggle
    ];

    protected $attributes = [
        'status_type' => true, // Default to active (true)
    ];

    // Accessor and Mutator for status_type
    public function getStatusTypeAttribute($value): bool
    {
        return $value === 'active';
    }

    public function setStatusTypeAttribute($value): void
    {
        $this->attributes['status_type'] = $value ? 'active' : 'inactive';
    }

    // Relationship methods remain the same
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

    public function events()
    {
        return $this->hasMany(Event::class, 'campaign_planning_id');
    }
}