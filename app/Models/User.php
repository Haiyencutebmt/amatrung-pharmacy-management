<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'role',
        'is_active',
        'legacy_permissions_json',
        'reset_code',
        'reset_code_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'is_active'               => 'boolean',
            'legacy_permissions_json' => 'array',
            'reset_code_expires_at'   => 'datetime',
        ];
    }

    // ── Helper phân quyền ──────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        if ($this->hasAnyRole(['admin', 'practitioner', 'staff'])) {
            return true;
        }
        return in_array($this->role, ['admin', 'staff']);
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        try {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            // Ignore if permission doesn't exist in Spatie yet
        }

        if ($this->role === 'staff') {
            $sensitivePerms = [
                'manage_inventory', 'dispense_prescriptions', 'create_medical_records',
                'create_prescriptions', 'view_medical_record_attachments', 'use_ai_suggestion', 'manage_users'
            ];
            if (in_array($permission, $sensitivePerms)) {
                return false; // MUST use Spatie for sensitive perms
            }

            $perms = $this->legacy_permissions_json ?? [];
            return in_array($permission, $perms);
        }

        return false;
    }

    public function setPermissionsAttribute($value)
    {
        $this->attributes['legacy_permissions_json'] = is_array($value) ? json_encode($value) : $value;
        unset($this->attributes['permissions']);
    }

    // ── Relationships ──────────────────────────────────────────

    /** Bệnh nhân mà user này liên kết (1 user - 1 patient) */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /** Bệnh án mà staff/admin này đã khám */
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'staff_id');
    }

    /** Đơn thuốc mà staff/admin này đã kê */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'staff_id');
    }

    /** Bài viết do user này tạo */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /** Bình luận do user này gửi */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /** Các mục từ điển thuốc nam đã lưu yêu thích */
    public function herbDictionaryFavorites()
    {
        return $this->belongsToMany(HerbDictionaryEntry::class, 'herb_dictionary_favorites', 'user_id', 'entry_id')->withTimestamps();
    }

    /** Các bài viết đã thích */
    public function likedArticles()
    {
        return $this->belongsToMany(Article::class, 'article_likes', 'user_id', 'article_id')->withTimestamps();
    }


}
