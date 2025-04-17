<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DictionaryItem extends Model
{
    protected $fillable = ['dictionary_id', 'name', 'value', 'description', 'order'];

    public function dictionary()
    {
        return $this->belongsTo(Dictionary::class);
    }
}