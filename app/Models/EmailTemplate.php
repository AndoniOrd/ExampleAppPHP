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
 *     @OA\Property(property="subject", type="string", example="Welcome to Our Service!"),
 *     @OA\Property(property="content", type="string", example="<h1>Hello {{name}}!</h1>..."),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content'
    ];

    /**
     * Get campaigns using this template
     */
    public function campaigns()
    {
        return $this->hasMany(CampaignPlanning::class, 'email_template_id');
    }
}