<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="EmailTemplate",
 *     title="Email Template",
 *     description="Email template model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Welcome Email"),
 *     @OA\Property(property="subject_line", type="string", example="Welcome to Our Service!"),
 *     @OA\Property(property="html_content", type="string", example="<h1>Hello {{name}}!</h1>..."),
 *     @OA\Property(property="plain_text_version", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject_line',
        'html_content',
        'plain_text_version',
        'creator',
        'creation_date',
        'last_updated_date',
        'category',
        'status',
        'preview_image_url',
        'from_name',
        'from_address'
    ];

    protected $casts = [
        'creation_date' => 'date',
        'last_updated_date' => 'date',
    ];

    /**
     * Get campaigns using this template
     */
    public function campaigns()
    {
        return $this->hasMany(CampaignPlanning::class, 'email_template_id');
    }
    
    /**
     * Get the user who created this template
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator');
    }
}