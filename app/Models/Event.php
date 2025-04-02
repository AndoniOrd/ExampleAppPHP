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
        'campaign_planning_id', // Add this to allow mass assignment
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        // Add if using soft deletes
        // 'deleted_at' => 'datetime',
    ];

    // Uncomment if using UUID as primary key
    // public function getRouteKeyName()
    // {
    //     return 'uuid';
    // }

    /**
     * Get the campaign planning associated with this event.
     */
    public function campaignPlanning()
    {
        return $this->belongsTo(CampaignPlanning::class, 'campaign_planning_id');
    }
}
