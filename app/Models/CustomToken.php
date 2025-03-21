<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;

class CustomToken extends PersonalAccessToken
{
    protected $table = 'custom_tokens';

    // Allow mass assignment for these fields
    protected $fillable = [
        'name',
        'token',
        'abilities',
    ];
}