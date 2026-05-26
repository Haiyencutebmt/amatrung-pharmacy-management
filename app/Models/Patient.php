<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 */
class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_code',
        'user_id',
        'full_name',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'guardian_name',
        'guardian_phone',
        'relationship',
        'note',
        'is_legacy_data',
        'legacy_source',
        'legacy_note',
        'imported_at',
        'imported_by',
        'legacy_date',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_legacy_data' => 'boolean',
            'imported_at' => 'datetime',
            'legacy_date' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    /** Tài khoản user liên kết (nếu có) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Tất cả bệnh án của bệnh nhân */
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /** Bệnh án gần nhất */
    public function latestMedicalRecord()
    {
        return $this->hasOne(MedicalRecord::class)->latestOfMany();
    }

    /** Danh sách lịch hẹn */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // ── Helper ────────────────────────────────────────────────

    /** Tính tuổi từ ngày sinh */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth
            ? $this->date_of_birth->age
            : null;
    }

    /** Nhãn giới tính tiếng Việt */
    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'male'   => 'Nam',
            'female' => 'Nữ',
            'other'  => 'Khác',
            default  => 'Chưa xác định',
        };
    }

    /**
     * Sinh mã bệnh nhân tự động dạng BN0001
     * Gọi trước khi tạo: Patient::generateCode()
     */
    public static function generateCode(): string
    {
        $last = static::orderBy('id', 'desc')->first();
        $next = $last ? ((int) substr($last->patient_code, 2)) + 1 : 1;
        return 'BN' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
