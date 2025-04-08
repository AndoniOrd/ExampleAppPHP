<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    // Uncomment if using UUIDs
    // use HasUuids;
    // use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'color',
        'starts_at',
        'ends_at',
        'status_type',
        'campaign_planning_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        // Add if using soft deletes
        // 'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-set ends_at when creating event
        static::creating(function ($model) {
            if (!$model->ends_at) {
                $model->ends_at = $model->starts_at;
            }
        });
    }
    public function campaignPlanning()
    {
        return $this->belongsTo(CampaignPlanning::class, 'campaign_planning_id');
    }
}