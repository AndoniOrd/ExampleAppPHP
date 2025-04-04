<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Dictionary extends Model
{
    protected $fillable = ['name', 'description'];

    public function items()
    {
        return $this->hasMany(DictionaryItem::class);
    }
}