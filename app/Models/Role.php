<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laratrust\Models\Role as RoleModel;
use Spatie\Permission\Traits\HasPermissions;


class Role extends  RoleModel

{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use HasFactory;
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];
}