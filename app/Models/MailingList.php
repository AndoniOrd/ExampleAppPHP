<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailingList extends Model
{
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
