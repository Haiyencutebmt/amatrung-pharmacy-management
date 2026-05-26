<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HerbDictionaryFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entry_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entry()
    {
        return $this->belongsTo(HerbDictionaryEntry::class, 'entry_id');
    }
}
