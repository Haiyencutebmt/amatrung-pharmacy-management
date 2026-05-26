<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HerbDictionaryEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'name',
        'slug',
        'scientific_name',
        'other_names',
        'family',
        'plant_part',
        'properties',
        'basic_info',
        'effects',
        'usage_notes',
        'safety_warning',
        'status',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(HerbDictionaryImage::class, 'entry_id')->orderBy('sort_order')->orderBy('id');
    }

    public function favorites()
    {
        return $this->hasMany(HerbDictionaryFavorite::class, 'entry_id');
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'herb_dictionary_favorites', 'entry_id', 'user_id')->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $image = $this->images->first();

        return $image ? asset('storage/' . $image->image_path) : null;
    }

    public function getShortInfoAttribute(): string
    {
        return Str::limit(strip_tags($this->basic_info ?: $this->effects ?: ''), 130);
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('favorites')) {
            return $this->favorites->contains('user_id', $user->id);
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
