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
        'ends_at'
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
}