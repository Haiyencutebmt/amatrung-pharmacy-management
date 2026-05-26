<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'content',
        'rating',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'rating' => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    /** Bài viết chứa bình luận này */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /** Người gửi bình luận */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    /** Chỉ lấy bình luận đã duyệt */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', 1);
    }

    /** Chỉ lấy bình luận chờ duyệt */
    public function scopePending($query)
    {
        return $query->where('is_approved', 0);
    }
}
