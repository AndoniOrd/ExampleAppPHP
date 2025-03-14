<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Add this line

class Permission extends Model // Add "extends Model" here
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];
}