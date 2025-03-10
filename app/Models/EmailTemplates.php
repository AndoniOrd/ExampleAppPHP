<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EmailTemplateStatus;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $subject_line
 * @property string $html_content
 * @property string $plain_text_version
 * @property string $creator
 * @property \Illuminate\Support\Carbon $creation_date
 * @property \Illuminate\Support\Carbon $last_updated_date
 * @property string $category
 * @property EmailTemplateStatus $status
 * @property string $preview_image_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creatorUser
 */
class EmailTemplates extends Model
{
    use HasFactory;

    protected $table = 'email_templates';

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
        'preview_image_url'
    ];

    protected $casts = [
        'creation_date' => 'date:Y-m-d',
        'last_updated_date' => 'date:Y-m-d',
        'status' => EmailTemplateStatus::class
    ];

    // Relación con el usuario creador
    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'creator', 'id');
    }

    // Accesor para el campo category/type
    public function getCategoryTypeAttribute()
    {
        return $this->attributes['category'];
    }
}