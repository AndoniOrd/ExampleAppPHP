<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="EmailProvider",
 *     title="Email Provider",
 *     description="Email service provider model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="SMTP Provider"),
 *     @OA\Property(property="host", type="string", example="smtp.example.com"),
 *     @OA\Property(property="port", type="integer", example=587),
 *     @OA\Property(property="username", type="string", example="user@example.com"),
 *     @OA\Property(property="password", type="string", example="secret"),
 *     @OA\Property(property="encryption", type="string", example="tls"),
 *     @OA\Property(property="from_email", type="string", example="noreply@example.com"),
 *     @OA\Property(property="from_name", type="string", example="Marketing Team"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class EmailProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_email',
        'from_name',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'port' => 'integer'
    ];
}