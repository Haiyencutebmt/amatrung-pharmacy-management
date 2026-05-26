<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HerbDictionaryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function entry()
    {
        return $this->belongsTo(HerbDictionaryEntry::class, 'entry_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
