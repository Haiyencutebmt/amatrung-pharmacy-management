<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapyService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'default_sessions',
        'default_instruction',
        'status',
    ];

    protected $casts = [
        'default_sessions' => 'integer',
    ];

    /**
     * Scope only active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
