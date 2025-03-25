<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements LaratrustUser
{
    use HasFactory, Notifiable, HasApiTokens, HasRolesAndPermissions;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'account_status',
        'creation_date',
        'last_login',
        'company_name',
        'company_address',
        'vat_tax_id',
        'industry',
        'company_size',
        'website',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function username()
    {
        return 'email';
    }
}