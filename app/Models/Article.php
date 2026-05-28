<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'duoc-lieu-bai-thuoc' => 'Kiến thức dược liệu/bài thuốc',
        'benh-hoc-phuong-phap-dieu-tri' => 'Bệnh học & Phương pháp điều trị',
        'cham-soc-suc-khoe-duong-sinh' => 'Chăm sóc sức khỏe & Dưỡng sinh',
        'tin-tuc-phong-kham' => 'Tin tức phòng khám',
        'y-hoc-hien-dai-ket-hop' => 'Góc nhìn Y học hiện đại kết hợp',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'summary',
        'slug',
        'content',
        'featured_image',
        'category',
        'tags',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    /** Tác giả bài viết */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Tất cả bình luận của bài viết */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /** Chỉ bình luận đã duyệt */
    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('is_approved', 1);
    }

    /** Những người dùng đã thích bài viết này */
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'article_likes')->withTimestamps();
    }

    /** Kiểm tra xem user hiện tại đã thích bài viết chưa */
    public function isLikedBy($user)
    {
        if (!$user) return false;
        return $this->likedByUsers()->where('user_id', $user->id)->exists();
    }

    // ── Scopes ────────────────────────────────────────────────

    /** Chỉ lấy bài đã đăng */
    public function scopePublished($query)
    {
        return $query->where('is_published', 1);
    }

    // ── Helper ────────────────────────────────────────────────

    /** Tự sinh slug từ title nếu chưa có */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    /** Đoạn mô tả ngắn (200 ký tự đầu, không HTML) */
    public function getExcerptAttribute(): string
    {
        if (!empty($this->summary)) {
            return Str::limit($this->summary, 200);
        }

        $cleaned = preg_replace('/<[^>]+>/', ' ', $this->content);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        return Str::limit(html_entity_decode(trim($cleaned), ENT_QUOTES, 'UTF-8'), 200);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Kiến thức dược liệu/bài thuốc';
    }
}
